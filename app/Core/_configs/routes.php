<?php

declare(strict_types = 1);

use Slim\App;
use Slim\Routing\RouteCollectorProxy;

use App\Core\Controllers\ModalController;
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
    $app->group('', function (RouteCollectorProxy $group) {
        $group->get('/', [HomeController::class, 'index'])->setName('home');
        $group->group('/profile', function (RouteCollectorProxy $profile) {
            $profile->get('', [ProfileController::class, 'index']);
            $profile->post('', [ProfileController::class, 'update']);
            $profile->post('/update-password', [ProfileController::class, 'updatePassword']);
        });
    })->add(VerifyEmailMiddleware::class)->add(AuthMiddleware::class);

    $app->group('', function (RouteCollectorProxy $group) {
        $group->post('/modal', [ModalController::class, 'show']);
        $group->post('/logout', [AuthController::class, 'logOut']);
        $group->get('/verify', [VerifyController::class, 'index']);
        $group->get('/verify/{id}/{hash}', [VerifyController::class, 'verify'])
              ->setName('verify')
              ->add(ValidateSignatureMiddleware::class);
        $group->post('/verify', [VerifyController::class, 'resend'])
              ->setName('resendVerification')
              ->add(RateLimitMiddleware::class);
    })->add(AuthMiddleware::class);

    $app->group('', function (RouteCollectorProxy $guest) {
        $guest->get('/login', [AuthController::class, 'loginView']);
        $guest->get('/register', [AuthController::class, 'registerView']);
        $guest->post('/login', [AuthController::class, 'logIn'])
              ->setName('logIn')
              ->add(RateLimitMiddleware::class);
        $guest->post('/register', [AuthController::class, 'register'])
              ->setName('register')
              ->add(RateLimitMiddleware::class);
        $guest->post('/login/two-factor', [AuthController::class, 'twoFactorLogin'])
              ->setName('twoFactorLogin')
              ->add(RateLimitMiddleware::class);
        $guest->get('/forgot-password', [PasswordResetController::class, 'showForgotPasswordForm']);
        $guest->get('/reset-password/{token}', [PasswordResetController::class, 'showResetPasswordForm'])
              ->setName('password-reset')
              ->add(ValidateSignatureMiddleware::class);
        $guest->post('/forgot-password', [PasswordResetController::class, 'handleForgotPasswordRequest'])
              ->setName('handleForgotPassword')
              ->add(RateLimitMiddleware::class);
        $guest->post('/reset-password/{token}', [PasswordResetController::class, 'resetPassword'])
              ->setName('resetPassword')
              ->add(RateLimitMiddleware::class);
    })->add(GuestMiddleware::class);
};
