<?php

namespace App\Core\Components\Admin\Model\Tree\Collections;

use App\Core\Components\Admin\Model\Tree\CategoryItem;
use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;

class CategoryItems  extends ArrayCollection
{
    /**
     * @param  CategoryItem[]  $elements
     */
    public function __construct(private array $elements = [])
    {
        foreach ($this->elements as $element) {
            if (!($element instanceof CategoryItem)) {
                throw new InvalidArgumentException("Element must be an instance of CategoryItem");
            }
        }
        parent::__construct($elements);
    }

    /**
     * @param CategoryItem $element
     * @return void
     */
    public function add($element): void
    {
        if (!($element instanceof CategoryItem)) {
            throw new InvalidArgumentException("Element must be an instance of CategoryItem");
        }

        $this->elements[] = $element;
    }

    /**
     * @return CategoryItem[]
     */
    public function toArray(): array
    {
        return $this->elements;
    }
}