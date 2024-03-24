<?php
/**
 * @package  Categories.php
 * @copyright 24.03.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Admin\Model\Tree\Collections;

use App\Core\Components\Admin\Model\Tree\Category;
use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;

class Categories extends ArrayCollection
{

    /**
     * @param  Category[]  $elements
     */
    public function __construct(private array $elements = [])
    {
        foreach ($this->elements as $element) {
            if (!($element instanceof Category)) {
                throw new InvalidArgumentException("Element must be an instance of Category");
            }
        }
        parent::__construct($elements);
    }

    /**
     * @param Category $element
     * @return void
     */
    public function add($element): void
    {
        if (!($element instanceof Category)) {
            throw new InvalidArgumentException("Element must be an instance of Category");
        }

        $this->elements[] = $element;
    }

    /**
     * @return Category[]
     */
    public function toArray(): array
    {
        return $this->elements;
    }

}