<?php
/**
 * @package  container_bindings.php
 * @copyright 23.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);


use App\Core\Components\Catalog\Demo\DemoFilter;
use Psr\Container\ContainerInterface;
use Slim\Views\Twig;

use App\Core\ResponseFormatter;
use App\Core\Components\Catalog\Demo\DemoCatalogController;
use App\Core\Components\Catalog\Demo\DemoDataProvider;

return [
    DemoCatalogController::class => static function (ContainerInterface $container) {
        $dataProvider = new DemoDataProvider();
        $filterForm = new DemoFilter();
        return new DemoCatalogController(
            $container->get(Twig::class),
            $container->get(ResponseFormatter::class),
            $dataProvider,
            $filterForm,
        );
    }
];