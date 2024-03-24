<?php

declare(strict_types = 1);

use App\Core\Controllers\AdminController;
use App\Core\Controllers\ModalController;
use App\Core\Middleware\RoleMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Core\Controllers\AuthController;
use App\Core\Controllers\HomeController;
use App\Core\Controllers\PasswordResetController;
use App\Core\Controllers\ProfileController;
use App\Core\Controllers\VerifyController;
use App\Core\Middleware\AuthMiddleware;
use App\Core\Middleware\GuestMiddleware;
use App\Core\Middleware\RateLimitMiddleware;
use App\Core\Middleware\ValidateSignatureMiddleware;
use App\Core\Middleware\VerifyEmailMiddleware;

return static function (App $app) {


    $app->group('/admin', function (RouteCollectorProxy $group) {
        $group->get('', [AdminController::class, 'index'])->setName('admin');
    })->add(VerifyEmailMiddleware::class)
//        ->add(RoleMiddleware::admin())
        ->add(AuthMiddleware::class);
};
