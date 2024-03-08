<?php
/**
 * @package  AbstractDataProvider.php
 * @copyright 03.03.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Catalog\Providers;

use App\Core\Components\Catalog\Model\Filter\Collections\Filters;
use App\Core\Components\Catalog\Model\Filter\TableData;
use App\Core\Components\Catalog\Model\Filter\TableQueryParams;
use App\Core\Components\Catalog\Model\Table\Table;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Exception;

abstract class AbstractDataProvider implements CatalogDataProviderInterface, CatalogFilterInterface
{

    protected TableQueryParams $params;

    /**При необходимости переопределения порядка сортировки по умолчанию/page/perpage, переопределить конструктор или использовать params*/
    public function __construct(protected readonly EntityManager $entityManager, ?TableQueryParams $params = null)
    {
        if (is_null($params)) {
            $this->params = new TableQueryParams(new Filters());
        } else {
            $this->params = $params;
        }
    }

    public function get_table(array $records, TableQueryParams $params): Table
    {
        return Table::build($records, $this->head());
    }

    public function get_paginator(TableQueryParams $params): Paginator
    {
        $query = $this->get_query($params)
            ->setFirstResult($params->page * $params->perpage)
            ->setMaxResults($params->perpage);

        if ($params->orderBy) {
            $query->orderBy($params->orderBy, $params->orderDir->value);
        }

        return new Paginator($query);
    }

    public function get_params(): TableQueryParams
    {
        return $this->params;
    }


    /**
     * @param TableQueryParams $params
     * @return TableData
     * @throws Exception
     */
    public function get_table_data(TableQueryParams $params): TableData
    {
        $pagintor = $this->get_paginator($params);
        $pagintor->setUseOutputWalkers(false);
        $count = count($pagintor);
        $rows = array_map(
            function (array $item) {
                return $this->transform_data_row($item);
            },
            (array)$pagintor->getIterator()
        );
        return new  TableData(
            $rows, $params->page, $count, $params->perpage
        );
    }


}