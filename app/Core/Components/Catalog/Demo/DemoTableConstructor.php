<?php
/**
 * @package  TableConstructor.php
 * @copyright 19.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
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

class DemoTableConstructor
{

    /**Пример построения таблицы используя разные подходы:
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
            Row::build(['3', 'Тест4', 'хчч'], ['width' => 'color:100']),
            Row::build(['1', 'Тест', 'х'], ['style' => 'background-color: red']),
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

    public function shortExample(int $count = 0): string
    {
        ['head' => $head, 'rows' => $rows] = $this->data($count);
        return Table::build($rows, $head)->render();
    }

    public function shortExampleArray(int $count = 0): array
    {
        ['head' => $head, 'rows' => $rows] = $this->data($count);
        return Table::build($rows, $head, ['width' => '100%'])->toMap();
    }

}