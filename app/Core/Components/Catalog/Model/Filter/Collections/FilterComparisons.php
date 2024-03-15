<?php

namespace App\Core\Components\Catalog\Model\Filter\Collections;

use InvalidArgumentException;
use Doctrine\Common\Collections\ArrayCollection;
use App\Core\Components\Catalog\Model\Filter\FilterComparison;

class FilterComparisons extends ArrayCollection
{

    /**
     * @param FilterComparison[] $elements
     */
    public function __construct(
        array $elements = [],
    ) {
        foreach ($elements as $element) {
            if (!($element instanceof FilterComparison)) {
                throw new InvalidArgumentException("Element must be an instance of Filter");
            }
        }
        parent::__construct($elements);
    }

    public static function fromArray(array $array): FilterComparisons
    {
        $items = [];
        foreach ($array as $k => $v) {
            $items [] = new FilterComparison($k, $v);
        }
        return new FilterComparisons($items);
    }

    public function getKeys()
    {
        return array_map(static fn($e) => $e->name, $this->toArray());
    }

    public function get(string|int $key)
    {
        foreach ($this->toArray() as $el) {
            if ($el instanceof FilterComparison && $el->name == $key) {
                return $el;
            }
        }
        return null;
    }

}