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
 * $app->group('/demo_categories'...)
 *
 * DI прописан тут
 * app/Core/Components/Catalog/_configs/container_bindings.php
 * DemoCatalogController::class => ...
 *
 *
 **/
class DemoCatalogController extends CatalogController
{
    public const USE_CACHE = true;

    public function get_name(): string
    {
        return 'Демо таблица';
    }

    public static function get_index_route(): string
    {
        return '/demo_categories';
    }
}