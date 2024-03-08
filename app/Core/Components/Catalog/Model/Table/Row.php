<?php
/**
 * @package  TableHeader.php
 * @copyright 15.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Catalog\Model\Table;

use App\Core\Components\Catalog\Model\Table\Collections\Attributes;
use App\Core\Components\Catalog\Model\Table\Collections\Cells;
use Doctrine\Common\Collections\Collection;

readonly class Row
{

    public function __construct(
        public Cells $cells,
        public Attributes $attributes = new Attributes(),
    ) {
    }

    /**
     * @param  iterable|Collection  $cells
     * @param  Attributes  $attributes
     * @return Row
     */
    public static function build(
        iterable $cells = [],
        iterable $attributes = []
    ): Row {
        if ($cells instanceof Cells) {
            $realCells = $cells;
        } else {
            $new_cells = [];
            foreach ($cells as $c) {
                if ($c instanceof Cell) {
                    $new_cells [] = $c;
                } else {
                    $new_cells [] = new Cell((string)$c);
                }
            }
            $realCells = new Cells($new_cells);
        }

        return new Row($realCells, Attributes::fromArray($attributes));
    }

    public function render(): string
    {
        $html = "<tr $this->attributes>";
        foreach ($this->cells->toArray() as $cell) {
            $html .= $cell->render();
        }
        $html .= '</tr>';
        return $html;
    }

}