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
use App\Core\Contracts\SessionInterface;
use App\Core\Components\Catalog\Model\Filter\TableQueryParams;
use App\Core\Components\Catalog\Providers\CatalogFilterInterface;
use App\Core\Components\Catalog\Providers\CatalogDataProviderInterface;
use App\Core\Components\Catalog\Model\Pagination\PagingBar;


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
     */
    final public function index(Request $request, Response $response): Response
    {
        /**Cache**/
        /**Post+Get*/
        $data = array_merge((array)($request->getParsedBody() ?? []), $request->getQueryParams());
        if (!empty($data['clearCache']) && (bool)$data['clearCache']) {
            $this->clear_filters_from_cache();
        }
        $cache = $this->get_filters_from_cache();
        $content = $this->_get_content($request->getUri(), array_merge($cache, $data));

        return $this->twig->render(
            $response,
            static::TABLE_TEMPLATE,
            [
                'id' => $this->get_catalog_id(),
                'requestIndexRoute' => $this->get_index_route(),
                'tableHeading' => $this->get_name(),
                'filtersCatalog' => $content['filters']->render(),
                'tableContent' => $content['table']->render(),
                'tablePaginbar' => $content['paginbar']->render($this->twig),
            ],
        );
    }

    /**Метод вызываемый при поиске через Ajax
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    final public function filter(Request $request, Response $response): Response
    {
        /**Post+Get*/
        $data = array_merge((array)($request->getParsedBody() ?? []), $request->getQueryParams());
        $content = $this->_get_content($request->getUri()->withPath($this->get_index_route()), $data);
        $filters = $content['filters'];

        $current_filters = $filters->getValues();
        $cached_filters = $this->get_filters_from_cache();
        $filter_changed = !empty(array_diff($current_filters, $cached_filters));
        if ($filter_changed) {
            $this->save_filters_to_cache($current_filters);
        }


        $map = $content['table']->toMap();
        $map['filter_changed'] = $filter_changed;
        $map['paginbar'] = $content['paginbar']->render($this->twig);
        return $this->responseFormatter->asJson($response, $map);
    }

    /**
     * @param UriInterface $uri
     * @param array $data
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function _get_content(UriInterface $uri, array $data): array
    {
        $filters = $this->dataProvider->filters($data)->fillData($data);
        $params = TableQueryParams::fromArray(
            array_merge(
                $this->dataProvider->get_params()->toArray(),
                $data,
                ['filters' => $filters],
                $filters->getValues()
            )
        );
        $tableData = $this->dataProvider->get_table_data($params);

        $paginbar = new PagingBar(
            $tableData->totalRecords,
            $tableData->currentPage,
            $tableData->perPage,
            $uri
        );

        return [
            'table' => $this->dataProvider->get_table($tableData->records, $params),
            'filters' => $filters,
            'paginbar' => $paginbar
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
                $provider = new $className($container->get(EntityManagerInterface::class), $params);
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

    public static function routing(App $app, string $route): void
    {
        if (stripos($route, '/') !== 0) {
            $route = '/' . $route;
        }
        $reportName = substr($route, 1);
        $class = static::class;
        $app->group($route, function (RouteCollectorProxy $collectorProxy) use ($class, $reportName) {
            $collectorProxy->get('', [$class, 'index'])->setName($reportName);
            $collectorProxy->post('/filter', [$class, 'filter']);
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
