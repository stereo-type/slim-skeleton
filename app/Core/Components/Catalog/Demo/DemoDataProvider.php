<?php
/**
 * @package  TableDataProvider.php
 * @copyright 23.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Catalog\Demo;

use App\Core\Components\Catalog\Dto\Table\Attribute;
use App\Core\Components\Catalog\Dto\Table\Body;
use App\Core\Components\Catalog\Dto\Table\Cell;
use App\Core\Components\Catalog\Dto\Table\Collections\Attributes;
use App\Core\Components\Catalog\Dto\Table\Collections\Cells;
use App\Core\Components\Catalog\Dto\Table\Collections\Rows;
use App\Core\Components\Catalog\Dto\Table\Row;
use App\Core\Components\Catalog\Dto\Table\Table;
use App\Core\Components\Catalog\Providers\CatalogDataProviderInterface;

class DemoDataProvider implements CatalogDataProviderInterface
{


    /**Пример построения таблицы используя разные подходы:
     * Метод не используется для реального построения таблицы
     * 1) ООП
     * 2) Сокращенный вариант через хелперы (build)
     * 3) Смешанный вариант
     * @return string
     */
    public function render(): string
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


    private function data(int $count = 0): array
    {
        $head = ['№', 'Название', 'Управление'];
        $rows = [
            Row::build(['3', 'Тест4', 'хчч'], ['width' => 'color:100', 'class'=>'table-info']),
            Row::build(['1', 'Тест', 'х'], ['style' => 'background-color: red','class'=>'table-danger' ]),
            Row::build(['1221', 'Тест11', '3aaх'], ['style' => 'color:blue']),
            Row::build(['1', 'Тест', 'х'], ['style' => 'background-color: red']),
            Row::build(['1221', 'Тест11', '3aaх'], ['style' => 'color:blue']),
            Row::build(['1', 'Тест', 'х'], ['style' => 'background-color: red']),
            Row::build(['1221', 'Тест11', '3aaх'], ['style' => 'color:blue']),
            ['1221', 'Тест11', '3aaх'],
            ['1221', 'Тест11', '3aaх'],
            ['1221', 'Тест11', '3aaх'],
            ['1221', 'Тест11', '3aaх'],
            ['1221', 'Тест11', '3aaх'],
            ['1221', 'Тест11', '3aaх'],
        ];

        return ['head' => $head, 'rows' => $count > 0 && $count< count($rows) ? array_slice($rows,0, $count): $rows];
    }

    public function filter_data(array $filter): Table
    {
        ['head' => $head, 'rows' => $rows] = $this->data(3);
        return Table::build($rows, $head);
    }

    public function get_table(): Table
    {
        ['head' => $head, 'rows' => $rows] = $this->data();
        return Table::build($rows, $head);
    }
}