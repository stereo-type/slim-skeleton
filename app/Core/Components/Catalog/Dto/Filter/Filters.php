<?php
/**
 * @package  TableHeader.php
 * @copyright 15.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Catalog\Dto\Filter;


use App\Core\Components\Catalog\Dto\Filter\Type\Find;
use App\Core\Components\Catalog\Dto\Filter\Type\PerPage;
use InvalidArgumentException;
use App\Core\Components\Catalog\Dto\Filter\Type\Filter;
use Doctrine\Common\Collections\ArrayCollection;

class Filters extends ArrayCollection
{

    /**
     * @param  Filter[]  $elements
     */
    public function __construct(private array $elements = [], private readonly bool $perpage = true, private readonly bool $find = true)
    {
        foreach ($this->elements as $element) {
            if (!($element instanceof Filter)) {
                throw new InvalidArgumentException("Element must be an instance of Filter");
            }
        }
        $has_perpage = false;
        $has_find = false;
        foreach ($this->elements as $element) {
            if($element instanceof  PerPage) {
                $has_perpage = true;
            }
            if($element instanceof  Find) {
                $has_find = true;
            }
        }
        if($this->perpage && !$has_perpage) {
            $this->elements[] = PerPage::build();
        }

        if($this->find && !$has_find) {
            $this->elements[] = Find::build();
        }
        parent::__construct($elements);
    }


    /**
     * @param  Filter  $element
     * @return void
     */
    public function add($element): void
    {
        if (!($element instanceof Filter)) {
            throw new InvalidArgumentException("Element must be an instance of Filter");
        }

        $this->elements[] = $element;
    }

    /**
     * @return Filter[]
     */
    public function toArray(): array
    {
        return $this->elements;
    }

    public function __toString(): string
    {
        return implode(' ', $this->toArray());
    }

    public function render(): string
    {
        return (string)$this;
    }


}