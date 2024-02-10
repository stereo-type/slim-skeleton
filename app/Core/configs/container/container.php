<?php

declare(strict_types=1);

use DI\ContainerBuilder;

$containerBuilder = new ContainerBuilder();

$containerBuilder->addDefinitions(__DIR__.'/container_bindings.php');
$project_bindings = CONFIG_PATH.'/container_bindings.php';
/**Если есть в проекте привязки контейнера, добавляем их*/
if (file_exists($project_bindings)) {
    $containerBuilder->addDefinitions($project_bindings);
}

return $containerBuilder->build();
