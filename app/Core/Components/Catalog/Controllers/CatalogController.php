<?php

declare(strict_types=1);

namespace App\Core\Components\Catalog\Controllers;

use InvalidArgumentException;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

use App\Core\ResponseFormatter;
use App\Core\Components\Catalog\Demo\DemoTableConstructor;
use App\Core\Components\Catalog\Enums\TableContentType;
use App\Core\Components\Catalog\Providers\CatalogDataProviderInterface;


/**Класс для построения контроллеров таблиц без привязки к сущностям (Entity)*/
abstract class CatalogController
{

    /**Шаблон представления фильтров и таблицы, при необходимости переопределить*/
    protected const TABLE_TEMPLATE = 'catalog/index.twig';

    /**Тип возвращаемого контента из поисковых запросов, при необходимости переопределить*/
    protected const TABLE_CONTENT_TYPE = TableContentType::html->value;


    public function __construct(
        protected readonly Twig $twig,
        protected readonly ResponseFormatter $responseFormatter,
        protected readonly CatalogDataProviderInterface $dataProvider
    ) {
    }

    /**Метод получения названия таблицы, используется в качестве заголовка
     * @return string
     */
    abstract public function get_name(): string;


    function filterData($filters)
    {
        // Здесь должна быть логика фильтрации данных в соответствии с переданными параметрами
        // В этом примере просто возвращается статический набор данных
        $data = [
            ['id' => 1, 'name' => 'Item 1', 'description' => 'Description 1'],
            ['id' => 2, 'name' => 'Item 2', 'description' => 'Description 2'],
            ['id' => 3, 'name' => 'Item 3', 'description' => 'Description 3'],
            // Добавьте другие данные по мере необходимости
        ];

        // Пример простой фильтрации данных
        $filteredData = array_filter($data, static function ($item) use ($filters) {
            $valid = true;
            foreach ($filters as $key => $value) {
                if (!empty($value) && isset($item[$key]) && $item[$key] !== $value) {
                    $valid = false;
                    break;
                }
            }
            return $valid;
        });

        return array_values($filteredData);
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
        $t = new DemoTableConstructor();
        $tableContent = $t->shortExample();

        return $this->twig->render(
            $response,
            static::TABLE_TEMPLATE,
            [
                'tableHeading' => $this->get_name(),
                'tableContent' => $tableContent,
                'contentType'  => static::TABLE_CONTENT_TYPE,
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
        $data = $request->getParsedBody();
        $contentType = $data['content_type'] ?? static::TABLE_CONTENT_TYPE;

//        $filteredData = $this->filterData($data); // Функция, которая фильтрует данные
        $t = new DemoTableConstructor();

        if ($contentType === TableContentType::html->value) {
            $filteredData = $t->shortExample(3);
            $response->getBody()->write($filteredData);
            return $response;
        }

        if ($contentType === TableContentType::json->value) {
            $filteredData = $t->shortExampleArray(3);
            return $this->responseFormatter->asJson($response, $filteredData);
        }

        throw new InvalidArgumentException("Invalid content type $contentType");
    }

}
