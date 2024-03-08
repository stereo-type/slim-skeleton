<?php
/**
 * @package  Container.php
 * @copyright 04.03.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core;

use Psr\Container\ContainerInterface;

class Container
{
    private static ?ContainerInterface $container = null;

    public static function get_container(): ContainerInterface
    {
        if (!is_null(self::$container)) {
            return self::$container;
        }
        self::$container = include ROOT_PATH.'/bootstrap.php';
        return self::$container;
    }

}