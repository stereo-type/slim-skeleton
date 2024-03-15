<?php

declare(strict_types = 1);

use Dotenv\Dotenv;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__.'/app/Core/_configs/path_constants.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
return require CORE_CONFIG_PATH . '/container/container.php';
