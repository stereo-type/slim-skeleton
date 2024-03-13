<?php
/**
 * @package  TableDataProvider.php
 * @copyright 23.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Catalog\Demo;

use DateTime;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use Slim\Views\Twig;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Common\Collections\Expr\Comparison;

use App\Core\Entity\User;
use App\Core\Components\Catalog\Enum\FilterType;
use App\Core\Components\Catalog\Enum\ParamType;
use App\Core\Components\Catalog\Model\Filter\Collections\FilterComparisons;
use App\Core\Components\Catalog\Model\Filter\Collections\Filters;
use App\Core\Components\Catalog\Model\Filter\TableQueryParams;
use App\Core\Components\Catalog\Model\Filter\Type\Filter;
use App\Core\Components\Catalog\Model\Table\Attribute;
use App\Core\Components\Catalog\Model\Table\Body;
use App\Core\Components\Catalog\Model\Table\Cell;
use App\Core\Components\Catalog\Model\Table\Collections\Attributes;
use App\Core\Components\Catalog\Model\Table\Collections\Cells;
use App\Core\Components\Catalog\Model\Table\Collections\Rows;
use App\Core\Components\Catalog\Model\Table\Row;
use App\Core\Components\Catalog\Model\Table\Table;
use App\Core\Components\Catalog\Providers\AbstractDataProvider;

class DemoDataProvider extends AbstractDataProvider
{
    public function __construct(EntityManager $entityManager, ContainerInterface $container)
    {
        parent::__construct($entityManager, $container, new TableQueryParams(orderBy: 'u.id'));
    }


    /**Пример построения таблицы используя разные подходы:
     * Метод не используется для реального построения таблицы
     * 1) ООП
     * 2) Сокращенный вариант через хелперы (build)
     * 3) Смешанный вариант
     * @return string
     */
    public function testCase(): string
    {
        $rows = [
            new Row(
                new Cells(
                    [
                        new Cell('3'),
                        new Cell('Тест4'),
                        new Cell(
                            'хчч',
                            new Attributes([new Attribute('width', 100)])
                        ),
                    ]
                ),

            ),
            Row::build(
                [
                    new Cell('№'),
                    new Cell('Название'),
                    new Cell('Управление'),
                ],
                new Attributes(
                    [
                        new Attribute('width', '50%')
                    ]
                )
            ),
            Row::build(['1', 'Тест', 'х'], Attributes::fromArray(['style' => 'background-color: red'])),
            Row::build(['1221', 'Тест11', '3aaх'], ['style' => 'color:blue']),
        ];
        $table = new Table(new Body(new Rows($rows)), attributes: new Attributes([new Attribute('width', '100%')]));
        return $table->render();
    }


    private function def_rows(): array
    {
        return [
            ['1221', 'Тест11', '3aaх'],
            ['1221', 'Тест11', '3aaх'],
            ['1221', 'Тест11', '3aaх'],
            Row::build(['3', 'Тест4', 'хчч'], ['width' => 'color:100', 'class' => 'table-info']),
            Row::build(['1', 'Тест', 'х'], ['style' => 'background-color: red', 'class' => 'table-danger']),
            Row::build(['1221', 'Тест11', '3aaх'], ['style' => 'color:blue']),
            Row::build(['1', 'Тест', 'х'], ['style' => 'background-color: red']),
            Row::build(['1221', 'Тест11', '3aaх'], ['style' => 'color:blue']),
            Row::build(['1', 'Тест', 'х'], ['style' => 'background-color: red']),
            Row::build(['1221', 'Тест11', '3aaх'], ['style' => 'color:blue']),
            ['1221', 'Тест11', '3aaх'],
            ['1221', 'Тест11', '3aaх'],
            ['1221', 'Тест11', '3aaх'],
        ];
    }


    public function head(): array
    {
        return ['№', 'Имя', 'Email', 'Подтвержден', 'Управление'];
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function filters(array $filterData): Filters
    {
        $filters = [];
        $filters[] = Filter::create(FilterType::input, 'id', ['placeholder' => 'ID'], paramType: ParamType::PARAM_INT);
        $filters[] = Filter::create(FilterType::input, 'name', ['placeholder' => 'Имя']);
        $filters[] = Filter::create(
            FilterType::input,
            'description',
            ['placeholder' => 'Тест разметки, не участвует в фильтрации'],
            length: 4
        );
        $filters[] = Filter::create(
            FilterType::input,
            'description4',
            ['placeholder' => 'Тест разметки, не участвует в фильтрации'],
            length: 4
        );
        $filters[] = Filter::create(FilterType::space, 'space1', length: 1);
        $filters[] = Filter::create(
            FilterType::space,
            'space2',
            defaultValue: '<div class="alert alert-info m-0 py-1 h-100"> Это спейсер </div>',
            length: 3
        );
        $filters[] = Filter::create(FilterType::input, 'description6', length: 2);
        $filters[] = Filter::create(
            FilterType::select,
            'desction7',
            params: ['options' => ['12', '3', '23']],
            length: 2
        );
        $filters[] = Filter::create(
            FilterType::perpage,
            'perpage',
            Attributes::fromArray(['style' => 'grid-column: 10;']),
            defaultValue: 2,
            params: ['options' => ['2' => '2', '4' => '4', '8' => '8']],
            length: 1
        );
        return new Filters($filters);
    }

    /**
     * @param TableQueryParams $params
     * @return QueryBuilder
     * @throws QueryException
     */
    public function get_query(TableQueryParams $params): QueryBuilder
    {
        $alies = 'u';
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select("$alies.id", "$alies.name", "$alies.email", "$alies.verifiedAt")
            ->from(User::class, $alies);

        $allowed = FilterComparisons::fromArray(
            ['id' => Comparison::EQ, 'name' => Comparison::CONTAINS]
        );
        return $params->filters->fill_query_builder($qb, $alies, $allowed);
    }

    public function transform_data_row(Twig $twig, array $item): array
    {
        $verified = $item['verifiedAt'] instanceof DateTime;
        return [
            $item['id'],
            $item['name'],
            $item['email'],
            new Cell(
                $verified ? '<i class="bi bi-check" style="font-size: xx-large;"></i>'
                    : '<i class="bi bi-x" style="font-size: xx-large;"></i>',
                Attributes::fromArray(['style' => $verified ? 'color: lightgreen;' : 'color: red;'])
            ),
            ''
        ];
    }
}