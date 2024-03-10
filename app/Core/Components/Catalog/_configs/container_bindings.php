<?php
/**
 * @package  container_bindings.php
 * @copyright 23.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

use App\Core\Components\Catalog\Demo\DemoCatalogController;
use App\Core\Components\Catalog\Demo\DemoDataProvider;
use App\Core\Components\Catalog\Demo\DemoUserCatalogController;
use App\Core\Components\Catalog\Demo\DemoUserDataProvider;

return [
    ...DemoCatalogController::binding(DemoDataProvider::class),
    ...DemoUserCatalogController::binding(DemoUserDataProvider::class),
];