<?php
/**
 * @package  DemoCatalog.php
 * @copyright 23.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Catalog\Demo;

use App\Core\Components\Catalog\Controllers\CatalogController;


/**
 * маршруты прописаны тут
 * app/Core/Components/Catalog/_configs/routes.php
 *
 **/
class DemoCatalogController extends CatalogController
{

    public function get_name(): string
    {
        return 'Демо таблица';
    }
}