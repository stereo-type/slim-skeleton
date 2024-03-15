<?php
/**
 * @package  DataTableQueryParams.php
 * @copyright 23.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Catalog\Model\Filter;


readonly class TableData
{
    public int $totalPage;

    public function __construct(
        public array $records,
        public int $currentPage,
        public int $totalRecords,
        public int $perPage,

    ) {
        $this->totalPage = (int)ceil($totalRecords / $perPage);
    }


}