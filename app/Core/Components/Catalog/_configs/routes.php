<?php
/**
 * @package  routes.php
 * @copyright 14.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Core\Middleware\AuthMiddleware;
use App\Features\Category\Controllers\CategoryController;

return static function (App $app) {
//    /**Добавлять тут свои маршруты*/
//    $app->group('/categories', function (RouteCollectorProxy $categories) {
//        $categories->get('', [CategoryController::class, 'index'])->setName('categories');
////        $categories->post('', [CategoryController::class, 'create']);
////        TODO temp
//        $categories->get('/create', [CategoryController::class, 'create']);
//
////        $categories->get('/{category}', [CategoryController::class, 'get']);
////        $categories->post('/{category}', [CategoryController::class, 'update']);
////        $categories->delete('/{category}', [CategoryController::class, 'delete']);
//    })->add(AuthMiddleware::class);


    $app->group('/categories', function (RouteCollectorProxy $categories) {
        $categories->get('', [CategoryController::class, 'index'])->setName('categories');
        $categories->post('/filter', [CategoryController::class, 'filter']);
    })->add(AuthMiddleware::class);



};
