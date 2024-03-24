<?php
/**
 * @package  routes.php
 * @copyright 14.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

use App\Core\Middleware\RoleMiddleware;
use Slim\App;
use App\Core\Config;
use App\Core\Enum\AppEnvironment;
use App\Core\Components\Catalog\Demo\DemoCatalogController;
use App\Core\Components\Catalog\Demo\DemoUserCatalogController;

return static function (App $app) {
    $env = $app->getContainer()?->get(Config::class)->get('app_environment') ?? '';
    if (AppEnvironment::isDevelopment($env)) {
        DemoCatalogController::routing($app);
        DemoUserCatalogController::routing($app);
    }
};
