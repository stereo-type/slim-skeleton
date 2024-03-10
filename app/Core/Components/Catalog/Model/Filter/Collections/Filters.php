<?php
/**
 * @package  TableHeader.php
 * @copyright 15.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Catalog\Model\Filter\Collections;


use App\Core\Components\Catalog\Model\Filter\Type\Clear;
use App\Core\Components\Catalog\Model\Filter\Type\Filter;
use App\Core\Components\Catalog\Model\Filter\Type\Page;
use App\Core\Components\Catalog\Model\Filter\Type\PerPage;
use App\Core\Components\Catalog\Model\Filter\Type\Search;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\Value;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\Query\QueryExpressionVisitor;
use Doctrine\ORM\QueryBuilder;
use InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class Filters extends ArrayCollection
{

    /**
     * @param Filter[] $elements
     * @param bool $perpage
     * @param bool $find
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(
        array $elements = [],
        private readonly bool $perpage = true,
        private readonly bool $find = true,
        private readonly bool $clear = true,
    ) {
        foreach ($elements as $element) {
            if (!($element instanceof Filter)) {
                throw new InvalidArgumentException("Element must be an instance of Filter");
            }
        }
        $has_perpage = $has_find = $has_page = $has_clear = false;
        foreach ($elements as $element) {
            if ($element instanceof PerPage) {
                $has_perpage = true;
            }
            if ($element instanceof Search) {
                $has_find = true;
            }
            if ($element instanceof Page) {
                $has_page = true;
            }
            if ($element instanceof Clear) {
                $has_clear = true;
            }
        }
        if ($this->perpage && !$has_perpage) {
            $elements[] = PerPage::build();
        }

        if ($this->clear && !$has_clear) {
            $elements[] = Clear::build();
        }

        if ($this->find && !$has_find) {
            $elements[] = Search::build();
        }

        if (!$has_page) {
            $elements[] = Page::build();
        }
        parent::__construct($elements);
    }


    /**
     * @param Filter $element
     * @return void
     */
    public function add($element): void
    {
        if (!($element instanceof Filter)) {
            throw new InvalidArgumentException("Element must be an instance of Filter");
        }

        parent::add($element);
    }

    /**Переопределен ради phpdoc
     * @return Filter[]
     */
    public function toArray(): array
    {
        return parent::toArray();
    }

    public function __toString(): string
    {
        return implode(' ', $this->toArray());
    }

    public function render(): string
    {
        return (string)$this;
    }

    /**Метод устанавливает в фильтра значения которые пришли из поста с отчисткой этих значений
     * @param array $data
     * @param bool $force - установка несмотря ни на что
     * @return $this
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function fillData(array $data, bool $force = false): self
    {
        foreach ($this->toArray() as $element) {
            if (isset($data[$element->name])) {
                if (!empty($data[$element->name]) || $force) {
                    $element->set_param(Filter::FILTER_PARAM_VALUE, $element->paramType->clean($data[$element->name]));
                }
            }
        }
        return $this;
    }

    public function getValues(): array
    {
        $values = [];
        foreach ($this->toArray() as $element) {
            $value = $element->get_value();
            if (!is_null($value) && !$element::IGNORE_IN_FILTER_REQUEST) {
                $values[$element->name] = $value;
            }
        }
        return $values;
    }

    /**Упрощенный метод наполнения $queryBuilder данными фильтров
     * @param QueryBuilder $queryBuilder
     * @param string $alies
     * @param FilterComparisons $allowed поля которые разрешены для использования
     * в запросе из переданных значений фильтров
     * @return QueryBuilder
     * @throws QueryException
     */
    public function fill_query_builder(
        QueryBuilder $queryBuilder,
        string $alies,
        FilterComparisons $allowed
    ): QueryBuilder {
        $values = [];
        foreach ($this->getValues() as $k => $value) {
            if (in_array($k, $allowed->getKeys())) {
                $values[$k] = $value;
            }
        }
        if (empty($values)) {
            return $queryBuilder;
        }

        $and = $queryBuilder->expr()->andX();

        $visitor = new QueryExpressionVisitor([$alies]);
        foreach ($values as $k => $v) {
            $item = $allowed->get($k);
            if ($item != null) {
                $and->add($visitor->walkComparison(new Comparison($k, $item->condition, new Value($v))));
            }
        }
        return $queryBuilder->where($and)->setParameters($visitor->getParameters());
    }

}
