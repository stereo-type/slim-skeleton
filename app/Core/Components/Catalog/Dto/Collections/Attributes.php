<?php
/**
 * @package  TableHeader.php
 * @copyright 15.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Catalog\Dto\Collections;

use App\Core\Components\Catalog\Dto\Attribute;
use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;


class Attributes extends ArrayCollection
{

    /**
     * @param  Attribute[]  $elements
     */
    public function __construct(private array $elements = [])
    {
        foreach ($this->elements as $element) {
            if (!($element instanceof Attribute)) {
                throw new InvalidArgumentException("Element must be an instance of Row");
            }
        }
        parent::__construct($elements);
    }

    public function add($element): void
    {
        if (!($element instanceof Attribute)) {
            throw new InvalidArgumentException("Element must be an instance of Cell");
        }

        $this->elements[] = $element;
    }

    /**
     * @return Attribute[]
     */
    public function toArray(): array
    {
        return $this->elements;
    }

    public function __toString(): string
    {
        return implode(' ', $this->toArray());
    }

    public static function fromArray(iterable $array): Attributes
    {
        if ($array instanceof self) {
            return $array;
        }

        $attributes = [];
        foreach ($array as $key => $value) {
            if ($value instanceof Attribute) {
                $attributes [] = $value;
            } else {
                $attributes [] = new Attribute($key, $value);
            }
        }
        return new Attributes($attributes);
    }

}