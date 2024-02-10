<?php
/**
 * @package  config.php
 * @copyright 10.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

use Slim\App;

return static function (App $app) {
    $core_middleware = require CORE_CONFIG_PATH.'/middleware.php';
    $core_middleware($app);
    /**Добавлять тут свои middleware*/
};
