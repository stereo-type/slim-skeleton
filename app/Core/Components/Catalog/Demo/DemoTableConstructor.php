<?php
/**
 * @package  TableConstructor.php
 * @copyright 19.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Catalog\Demo;

use App\Core\Components\Catalog\Dto\Attribute;
use App\Core\Components\Catalog\Dto\Body;
use App\Core\Components\Catalog\Dto\Cell;
use App\Core\Components\Catalog\Dto\Collections\Attributes;
use App\Core\Components\Catalog\Dto\Collections\Cells;
use App\Core\Components\Catalog\Dto\Collections\Rows;
use App\Core\Components\Catalog\Dto\Row;
use App\Core\Components\Catalog\Dto\Table;

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

    public function shortExample(): string
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
            Row::build(['1', 'Тест', 'х'], ['style' => 'background-color: red']),
            Row::build(['1221', 'Тест11', '3aaх'], ['style' => 'color:blue']),
            Row::build(['1', 'Тест', 'х'], ['style' => 'background-color: red']),
            Row::build(['1221', 'Тест11', '3aaх'], ['style' => 'color:blue']),
            Row::build(['1', 'Тест', 'х'], ['style' => 'background-color: red']),
            ['1221', 'Тест11', '3aaх'],
        ];

        return Table::build($rows, $head, ['width' => '100%']);
    }

}