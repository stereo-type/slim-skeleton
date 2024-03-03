<?php

declare(strict_types=1);

namespace App\Core\Components\Catalog\Controllers;

use App\Core\Components\Catalog\Providers\CatalogFilterInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

use App\Core\ResponseFormatter;
use App\Core\Components\Catalog\Providers\CatalogDataProviderInterface;


/**Класс для построения контроллеров таблиц без привязки к сущностям (Entity)*/
abstract class CatalogController
{

    /**Шаблон представления фильтров и таблицы, при необходимости переопределить*/
    protected const TABLE_TEMPLATE = 'catalog/index.twig';

    public function __construct(
        protected readonly Twig $twig,
        protected readonly ResponseFormatter $responseFormatter,
        protected readonly CatalogDataProviderInterface $dataProvider,
        protected readonly CatalogFilterInterface $filterProvider,
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
     * @param  Response  $response
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    final public function index(Response $response): Response
    {
        return $this->twig->render(
            $response,
            static::TABLE_TEMPLATE,
            [
                'id'                => $this->get_catalog_id(),
                'requestIndexRoute' => $this->get_index_route(),
                /**TOdo do cache and pass data here**/
                'filtersCatalog'    => $this->filterProvider->filters([])->render(),
                'tableHeading'      => $this->get_name(),
                'tableContent'      => $this->dataProvider->get_table()->render(),
            ],
        );
    }

    /**Метод вызываемый при поиске через Ajax
     * @param  Request  $request
     * @param  Response  $response
     * @return Response
     */
    final public function filter(Request $request, Response $response): Response
    {
        $data = (array)($request->getParsedBody() ?? []);

        throw new \RuntimeException('sss');
        $filteredData = $this->dataProvider->filter_data($data);
        return $this->responseFormatter->asJson($response, $filteredData->toMap());
    }
}
