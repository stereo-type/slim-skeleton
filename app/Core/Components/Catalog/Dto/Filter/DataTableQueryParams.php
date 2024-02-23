<?php
/**
 * @package  DataTableQueryParams.php
 * @copyright 23.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Catalog\Dto\Filter;


readonly class DataTableQueryParams
{
    public function __construct(
        public int $start,
        public int $length,
        public int $draw,
        public string $orderBy = 'id',
        public string $orderDir = 'asc',
    ) {
    }
}