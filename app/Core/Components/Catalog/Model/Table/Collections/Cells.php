<?php
/**
 * @package  TableHeader.php
 * @copyright 15.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Catalog\Model\Table\Collections;

use App\Core\Components\Catalog\Model\Table\Cell;
use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;

class Cells extends ArrayCollection
{

    /**
     * @param  Cell[]  $elements
     */
    public function __construct(private array $elements = [])
    {
        foreach ($this->elements as $element) {
            if (!($element instanceof Cell)) {
                throw new InvalidArgumentException("Element must be an instance of Row");
            }
        }
        parent::__construct($elements);
    }

    /**
     * @param Cell $element
     * @return void
     */
    public function add($element): void
    {
        if (!($element instanceof Cell)) {
            throw new InvalidArgumentException("Element must be an instance of Cell");
        }

        $this->elements[] = $element;
    }

    /**
     * @return Cell[]
     */
    public function toArray(): array
    {
        return $this->elements;
    }

    public function toMap(): array
    {
        $result = [];
        foreach ($this->toArray() as $cell) {
            $result[] = [
                'attributes'  => $cell->attributes->toMap(),
                'data'        => $cell->data,
                'cell_params' => $cell->params->toMap(),
            ];
        }
        return $result;
    }
}