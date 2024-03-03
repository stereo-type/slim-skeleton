<?php
/**
 * @package  DataTableQueryParams.php
 * @copyright 23.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Catalog\Dto\Filter;


use App\Core\Components\Catalog\Dto\Filter\Type\Filter;

readonly class DataTableQueryParams
{
    /**
     * @param  Filters  $filters
     * @param  int  $page
     * @param  int  $perpage
     * @param  string  $orderBy
     * @param  string  $orderDir
     */
    public function __construct(
        public Filters $filters,
        public int $page = 0,
        public int $perpage = 10,
        public string $orderBy = 'id',
        public string $orderDir = 'asc',
    ) {
    }
    public function addFilter(Filter $filter): void
    {
        $this->filters->add($filter);
    }

}