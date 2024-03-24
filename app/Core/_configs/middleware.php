<?php

declare(strict_types=1);

use App\Core\Config;
use App\Core\Enum\AppEnvironment;
use App\Core\Middleware\CsrfFieldsMiddleware;
use App\Core\Middleware\ExceptionMiddleware;
use App\Core\Middleware\LangTranslation;
use App\Core\Middleware\OldFormDataMiddleware;
use App\Core\Middleware\StartSessionsMiddleware;
use App\Core\Middleware\ValidationErrorsMiddleware;
use App\Core\Middleware\ValidationExceptionMiddleware;
use Clockwork\Clockwork;
use Clockwork\Support\Slim\ClockworkMiddleware;
use Slim\App;
use Slim\Middleware\MethodOverrideMiddleware;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

/**
 * declare(strict_types=1);
 *
 * use Slim\App;
 * return static function (App $app) {
 * $core_middleware = require CORE_CONFIG_PATH.'/middleware.php';
 * $core_middleware($app);
 *
 * Добавить свои зависимости
 *
 * }
 **/


return static function (App $app) {
    $container = $app->getContainer();
    if ($container === null) {
        throw new RuntimeException('Container is null');
    }
    $config = $container->get(Config::class);

    if ($config === null) {
        throw new RuntimeException('Config is null');
    }

    $app->add(MethodOverrideMiddleware::class);
    $app->add(CsrfFieldsMiddleware::class);
    $app->add('csrf');
    $app->add(TwigMiddleware::create($app, $container->get(Twig::class)));
    $app->add(ValidationExceptionMiddleware::class);
    $app->add(ValidationErrorsMiddleware::class);
    $app->add(OldFormDataMiddleware::class);
    $app->add(ExceptionMiddleware::class);
    $app->add(StartSessionsMiddleware::class);
    $app->add(LangTranslation::class);
    if (AppEnvironment::isDevelopment($config->get('app_environment'))) {
        $app->add(new ClockworkMiddleware($app, $container->get(Clockwork::class)));
    }
    $app->addBodyParsingMiddleware();
    $app->addErrorMiddleware(
        (bool)$config->get('display_error_details'),
        (bool)$config->get('log_errors'),
        (bool)$config->get('log_error_details')
    );
};
