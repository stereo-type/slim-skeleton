<?php

declare(strict_types=1);

namespace App\Core\Enum;

use App\Core\Config;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

enum AppEnvironment: string
{
    case Development = 'development';
    case Production = 'production';

    public static function isProduction(string $appEnvironment): bool
    {
        return self::tryFrom($appEnvironment) === self::Production;
    }

    public static function isDevelopment(string $appEnvironment): bool
    {
        return self::tryFrom($appEnvironment) === self::Development;
    }

    /**
     * @param ContainerInterface $container
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function showErrorsDetails(ContainerInterface $container): bool
    {
        $env = $container->get(Config::class)->get('app_environment') ?? '';
        $dev = AppEnvironment::isDevelopment($env);
        if (!$dev) {
            return false;
        }
        return (bool)$container->get(Config::class)->get('display_error_details');
    }
}
