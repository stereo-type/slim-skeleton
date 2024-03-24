<?php
/**
 * @package  TableHeader.php
 * @copyright 15.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Catalog\Model\Table\Collections;

use App\Core\Components\Catalog\Model\Table\Attribute;
use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;


class Attributes extends ArrayCollection
{

    /**Метод слияния атрибутов, перезапись или добавление, добавление сработает только для списка JOINABLE*/
    final public const MERGE_OVERRIDE = 1;
    final public const MERGE_JOIN = 2;

    final public const JOINABLE = [
        'style',
        'class'
    ];

    /**
     * @param  Attribute[]  $elements
     */
    public function __construct(array $elements = [])
    {
        foreach ($elements as $element) {
            if (!($element instanceof Attribute)) {
                throw new InvalidArgumentException("Element must be an instance of Row");
            }
        }
        parent::__construct($elements);
    }

    /**
     * @param Attribute $element
     * @return void
     */
    public function add($element): void
    {
        if (!($element instanceof Attribute)) {
            throw new InvalidArgumentException("Element must be an instance of Cell");
        }

        parent::add($element);
    }

    /**Переопределен ради phpdoc
     * @return Attribute[]
     */
    public function toArray(): array
    {
        return parent::toArray();
    }

    public function toMap(): array
    {
        $result = [];
        foreach ($this->toArray() as $attribute) {
            $result[$attribute->key] = $attribute->value;
        }
        return $result;
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


    public function remove(string|int $key): ?Attribute
    {
        $removed = null;
        foreach ($this->toArray() as $index => $element) {
            if ($element->key === $key) {
                $removed = $element;
//                unset($this->elements[$index]);
                parent::remove($index);
                break;
            }
        }
        return $removed;
    }

    public static function mergeAttributes(
        int $mergeMode = self:: MERGE_OVERRIDE,
        Attributes ...$attributes
    ): Attributes {
        $_attr = [];
        foreach ($attributes as $collection) {
            foreach ($collection->toArray() as $attribute) {
                $key = $attribute->key;
                $value = $attribute->value;
                if (array_key_exists($key, $_attr)) {
                    if ($mergeMode === self::MERGE_OVERRIDE) {
                        $_attr[$key] = $value;
                    } elseif ($mergeMode === self::MERGE_JOIN && in_array($key, self::JOINABLE, true)) {
                        $_attr[$key] .= ' '.$value;
                    }
                } else {
                    $_attr[$key] = $value;
                }
            }
        }

        return self::fromArray($_attr);
    }


}