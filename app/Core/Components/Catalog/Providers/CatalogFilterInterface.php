<?php
/**
 * @package  CatalogDataPRoviderInterface.php
 * @copyright 23.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Catalog\Providers;


use App\Core\Components\Catalog\Model\Filter\Collections\Filters;

interface CatalogFilterInterface
{
    public function filters(array $filterData): Filters;

}