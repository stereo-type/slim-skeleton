<?php

declare(strict_types=1);

namespace App\Core\Components\Catalog\Controllers;

use Exception;
use InvalidArgumentException;

use Psr\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Psr\Http\Message\UriInterface;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Doctrine\ORM\EntityManagerInterface;

use App\Core\ResponseFormatter;
use App\Core\Constants\ServerStatus;
use App\Core\Contracts\SessionInterface;
use App\Core\Components\Catalog\Model\Filter\TableQueryParams;
use App\Core\Components\Catalog\Providers\CatalogFilterInterface;
use App\Core\Components\Catalog\Providers\CatalogDataProviderInterface;
use App\Core\Components\Catalog\Model\Pagination\PagingBar;
use App\Core\Components\Catalog\Model\Filter\Type\Page;
use App\Core\Components\Catalog\Model\Filter\Collections\Filters;


/**Класс для построения контроллеров таблиц без привязки к сущностям (Entity). Релизация:
 ** 1) Создать класс провайдер данных, имплементирующий {@link CatalogDataProviderInterface} и {@link CatalogFilterInterface}.
 ** Для общих случаев можно использовать обобщенный {@link AbstractDataProvider}
 ** 2) Реализовать методы интерфесов в провайдере
 ** 3) Создать контроллел наследник {@link CatalogController}, передав в него провайдер из пункта 1
 ** 4) Реализовать методы {@link CatalogController::get_name} и {@link CatalogController::get_index_route}
 ** 5) Забиндить класс в конейнер DI (можно использовать метод {@link CatalogController::binding})
 * */
abstract class CatalogController
{

    /**Шаблон представления фильтров и таблицы, при необходимости переопределить*/
    public const TABLE_TEMPLATE = 'catalog/index.twig';
    public const CACHE_CATALOG_KEY = '_component_catalog';
    /**Использовать ли кеширование*/
    public const USE_CACHE = true;

    public function __construct(
        protected readonly CatalogDataProviderInterface & CatalogFilterInterface $dataProvider,
        protected readonly Twig $twig,
        protected readonly ResponseFormatter $responseFormatter,
        protected readonly SessionInterface $session,
    ) {
    }


    /**Метод получения названия таблицы, используется в качестве заголовка
     * @return string
     */
    abstract public function get_name(): string;

    /**Метод получения основного маршрута, на котором будет выведен отчет (метод index),
     * отправка запросов фильтров будет на $this->get_index_route().'/filter'
     * @return string
     */
    abstract protected function get_index_route(): string;


    /**Получение id таблицы, по умолчанию - hash от имени класса
     * @return string
     */
    public function get_catalog_id(): string
    {
        return hash('md5', static::class);
    }

    /**Метод рендера таблицы при первичной загрузке
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws LoaderError
     * @throws NotFoundExceptionInterface
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    final public function index(Request $request, Response $response): Response
    {
        /**Post+Get*/
        $data = array_merge((array)($request->getParsedBody() ?? []), $request->getQueryParams());
        /**ClearCache*/
        if (!empty($data['clearCache']) && (bool)$data['clearCache']) {
            $this->clear_filters_from_cache();
            return $response->withHeader('Location', $this->get_index_route())->withStatus(ServerStatus::REDIRECT);
        }

        /**Cache**/
        $cache = static::USE_CACHE ? $this->get_filters_from_cache() : [];
        $data = array_merge($cache, $data);
        $filters = $this->dataProvider->filters($data)->fillData($data);
        $content = $this->_get_content($request->getUri(), $filters, $data);

        return $this->twig->render(
            $response,
            static::TABLE_TEMPLATE,
            [
                'id'                => $this->get_catalog_id(),
                'requestIndexRoute' => $this->get_index_route(),
                'tableHeading'      => $this->get_name(),
                'filtersCatalog'    => $filters->render(),
                'tableContent'      => $content['table']->render(),
                'tablePaginbar'     => $content['paginbar']->render($this->twig),
            ],
        );
    }

    /**Метод вызываемый при поиске через Ajax
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    final public function filter(Request $request, Response $response): Response
    {
        /**Post+Get*/
        $data = array_merge((array)($request->getParsedBody() ?? []), $request->getQueryParams());
        $filters = $this->dataProvider->filters($data)->fillData($data);

        $current_filters = $filters->getValues();
        $cached_filters = static::USE_CACHE ? $this->get_filters_from_cache() : [];
        $filter_diff = array_diff_assoc($current_filters, $cached_filters);
        $filter_changed = !empty($filter_diff);
        if ($filter_changed) {
            /**Если изменена не только страница, а какие то фильтра, то необходимо сбросить страницу на 0*/
            if (!(count($filter_diff) === 1 && isset($filter_diff['page'])) && !empty($cached_filters)) {
                $data['page'] = Page::INIT_PAGE;
                $filters->fillData(['page' => Page::INIT_PAGE], force: true);
            }
            if (static::USE_CACHE) {
                $this->save_filters_to_cache($filters->getValues());
            }
        }
        $content = $this->_get_content($request->getUri()->withPath($this->get_index_route()), $filters, $data);

        $map = $content['table']->toMap();
        $map['filter_changed'] = $filter_changed;
        $map['paginbar'] = $content['paginbar']->render($this->twig);
        $map['page'] = $content['page'];
        $map['per_page'] = $content['per_page'];
        return $this->responseFormatter->asJson($response, $map);
    }

    /**
     * @param UriInterface $uri
     * @param Filters $filters
     * @param array $data
     * @return array
     * @throws Exception
     */
    private function _get_content(UriInterface $uri, Filters $filters, array $data): array
    {
        $params = TableQueryParams::fromArray(
            array_merge(
                $this->dataProvider->get_params()->toArray(),
                $data,
                ['filters' => $filters],
                $filters->getValues()
            )
        );
        $tableData = $this->dataProvider->get_table_data($this->twig, $params->copyWith(page: $params->page - 1));

        $paginbar = new PagingBar(
            $tableData->totalRecords,
            $tableData->currentPage,
            $tableData->perPage,
            $uri
        );

        return [
            'table'    => $this->dataProvider->get_table($tableData->records, $params),
            'paginbar' => $paginbar,
            'page'     => $tableData->currentPage + 1,
            'per_page' => $tableData->perPage,
        ];
    }


    /**Метод обертка для упрощенного биднига в контейнере
     * @param string $className
     * @param TableQueryParams|null $params
     * @return mixed
     */
    public static function binding(string $className, ?TableQueryParams $params = null): array
    {
        return [
            static::class => static function (ContainerInterface $container) use ($className, $params) {
                $provider = new $className($container->get(EntityManagerInterface::class), $container, $params);
                $implements = class_implements($provider);
                if (!in_array(CatalogDataProviderInterface::class, $implements) || !in_array(
                        CatalogFilterInterface::class,
                        $implements
                    )) {
                    throw new InvalidArgumentException(
                        "Class $className must implements CatalogDataProviderInterface && CatalogFilterInterface"
                    );
                }
                return new static(
                    $provider,
                    $container->get(Twig::class),
                    $container->get(ResponseFormatter::class),
                    $container->get(SessionInterface::class)
                );
            }
        ];
    }

    protected static function additional_routes(RouteCollectorProxy $collectorProxy): void
    {
    }

    public static function routing(App $app, string $route): void
    {
        if (stripos($route, '/') !== 0) {
            $route = '/' . $route;
        }
        $reportName = substr($route, 1);
        $class = static::class;
        $method = 'additional_routes';
        $app->group($route, function (RouteCollectorProxy $collectorProxy) use ($class, $reportName, $method) {
            $collectorProxy->get('', [$class, 'index'])->setName($reportName);
            $collectorProxy->post('/filter', [$class, 'filter']);
            $class::$method($collectorProxy);
        });
    }

    private function _class_cache_key(): string
    {
        return md5(static::class);
    }

    private function get_filters_from_cache(): array
    {
        if (!$this->session->has(self::CACHE_CATALOG_KEY)) {
            return [];
        } else {
            $value = $this->session->get(self::CACHE_CATALOG_KEY, []);
            if (!is_array($value)) {
                return [];
            }
            $current_data = $value[$this->_class_cache_key()] ?? [];
            return $current_data['filters'] ?? [];
        }
    }

    private function save_filters_to_cache(array $values): void
    {
        if (!$this->session->has(self::CACHE_CATALOG_KEY)) {
            $this->session->put(self::CACHE_CATALOG_KEY, []);
        }

        $value = $this->session->get(self::CACHE_CATALOG_KEY, []);
        if (!is_array($value)) {
            /**save not consistent data*/
            $value = [];
        }
        $value[$this->_class_cache_key()] = ['filters' => $values];
        $this->session->put(self::CACHE_CATALOG_KEY, $value);
    }

    private function clear_filters_from_cache(): void
    {
        if ($this->session->has(self::CACHE_CATALOG_KEY)) {
            $value = $this->session->get(self::CACHE_CATALOG_KEY, []);
            if (is_array($value) && array_key_exists($this->_class_cache_key(), $value)) {
                unset($value[$this->_class_cache_key()]);
                $this->session->put(self::CACHE_CATALOG_KEY, $value);
            }
        }
    }

}
