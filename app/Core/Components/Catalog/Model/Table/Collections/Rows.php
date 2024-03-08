<?php
/**
 * @package  TableHeader.php
 * @copyright 15.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Catalog\Model\Table\Collections;

use App\Core\Components\Catalog\Model\Table\Row;
use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;

class Rows extends ArrayCollection
{

    /**
     * @param  Row[]  $elements
     */
    public function __construct(private array $elements = [])
    {
        foreach ($this->elements as $element) {
            if (!($element instanceof Row)) {
                throw new InvalidArgumentException("Element must be an instance of Row");
            }
        }
        parent::__construct($elements);
    }


    /**
     * @param  Row  $element
     * @return void
     */
    public function add($element): void
    {
        if (!($element instanceof Row)) {
            throw new InvalidArgumentException("Element must be an instance of Row");
        }

        $this->elements[] = $element;
    }

    /**
     * @return Row[]
     */
    public function toArray(): array
    {
        return $this->elements;
    }

    public function toMap(): array
    {
        $result = [];
        foreach ($this->toArray() as $row) {
            $result[] = [
                'attributes' => $row->attributes->toMap(),
                'cells'      => $row->cells->toMap(),
            ];
        }
        return $result;
    }

    /**В этом методе атрибуты передаваемые для всех строк одинаковые
     * @param  iterable  $array
     * @param  iterable  $attributes
     * @return Rows
     */
    public static function fromArray(iterable $array, iterable $attributes = []): Rows
    {
        if ($array instanceof self) {
            return $array;
        }

        $rows = [];
        foreach ($array as $value) {
            if ($value instanceof Row) {
                $rows [] = $value;
            } else {
                if (!is_iterable($value)) {
                    throw  new InvalidArgumentException('Value must be itterable. Type '.gettype($value).' provided');
                }
                $rows [] = Row::build($value, $attributes);
            }
        }
        return new Rows($rows);
    }

}