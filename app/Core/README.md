# Custom Slim Skeleton

## Готовим проект

1. Создаем проект
2. В корне проекта создаем папку app
3. Переходим в папку app, устанaвливаем
   сабмодуль `git submodule add https://github.com/stereo-type/slim-skeleton-core.git Core`
4. Переходим в папку докера [/app/Core/_docker](/app/Core/_docker). Создаем файл .env, копируем в него
   содержимое [.env.example](/app/Core/_docker/.env.example) и заполняем данными
5. Копируем из папки [app/Core/_copy](/app/Core/_copy) все файлы в корень проекта
6. В корне проекта создаем файл .env и копируем в него содержимое [.env.example](/.env.example)
7. Заполняем файл окружения необходимыми параметрами
8. В корне основного проекта создаем папки storage и public

9. Запускаем контейнеры командой `docker-compose up -d` из папки [/app/Core/_docker](/app/Core/_docker)
10. Входим в контейнер командой `docker exec -it slim-php-skeleton bash`. Дальнейшие команды будем выполнять из
    контейнера
11. Устанавливаем зависимости PHP командой `composer install`
12. Устанавливаем зависимости JS командой `npm i`
13. Создаем миграции командной `php cli doctrine:migrations:diff`
14. Выполняем миграции командной `php cli doctrine:migrations:migrate`

## Добавляем в проект директории конфигов

1. В корне проекта создаем папку _configs
2. Добавляем в папку __configs файл config.php. В нем указываются дополнительные конфиги
    ```php
    <?php
    
    declare(strict_types=1);

    $def_config = require CORE_CONFIG_PATH.'/core_config.php';

    $config = [
    /**Дополнить своими конфигами*/
    ];

    return $def_config + $config;
    ```
3. Добавляем в папку __configs файл container_bindings.php. Вызывать ничего не надо, вызовется автоматически из
   app/Core/_configs/container/container.php
    ```php
   <?php
    declare(strict_types=1);
    
    return [
    
    ];
   ```
4. Добавляем папку __configs файл middleware.php
   ```php
   <?php

   declare(strict_types=1);
   
   use Slim\App;
   
   return static function (App $app) {
   $core_middleware = require CORE_CONFIG_PATH.'/middleware.php';
   $core_middleware($app);
   /**Добавлять тут свои middleware*/
   };

   ```
5. Добавляем в папку __configs файл routes.php
   ```php
   <?php
   declare(strict_types=1);
   
   
   use Slim\App;
   
   return static function (App $app) {
       $core_router = require CORE_CONFIG_PATH.'/routes.php';
       $core_router($app);
   };

   ```

## Добавляем в проект директории aссетов
1. В корне проекта добавляем папку _assets
2. В папке могут находиться шаблоны twig - templates
3. В папке могут находиться скрипты JS и TS - js
4. В папке могут находиться картинки images
5. В папке могут находиться шрифты fonts
6. В папке могут находиться стили css и scss

## Проекте подключен Bootstrap 5

Для инициализации использовать скрипт [app/Core/_assets/js/app.ts](/app/Core/_assets/js/app.ts) или шаблон [app/Core/_assets/templates/layout.twig](/app/Core/_assets/templates/layout.twig)

### ПОЛЕЗНЫЕ КОМАНДЫ ###

1. docker stop $(docker ps -a -q) && docker rm $(docker ps -a -q) && docker rmi $(docker images -a -q)
2. cd /home/USER/projects/testproject/docker && docker-compose down && docker-compose up -d --build && chmod -R 777
   /home/USER/projects/testproject/dockerstorage
3. docker exec -it testproject-app /bin/bash
4. ssh -R 4510:127.0.0.1:9003 -p 5022 USER@192.168.1.101

