<?php
/**
 * @package  CatalogDataPRoviderInterface.php
 * @copyright 23.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Catalog\Providers;

use Slim\Views\Twig;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

use App\Core\Components\Catalog\Model\Filter\TableData;
use App\Core\Components\Catalog\Model\Filter\TableQueryParams;
use App\Core\Components\Catalog\Model\Table\Table;
use App\Core\Components\Catalog\Model\Table\Collections\Rows;

interface CatalogDataProviderInterface
{
    /**Методы которые необзодимо определить*/

    /**Шапка таблицы, может быть в виде коллекци {@link Rows}, что позволит стилизоват ее целиком */
    public function head(): iterable;

    public function get_query(TableQueryParams $params): QueryBuilder;

    public function transform_data_row(Twig $twig, array $item): array;

    public function get_table(array $records, TableQueryParams $params): Table;

    public function get_table_data(Twig $twig, TableQueryParams $params): TableData;

    public function get_paginator(TableQueryParams $params): Paginator;

    public function get_params(): TableQueryParams;

}