<?php
/**
 * @package  Builder.php
 * @copyright 24.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\_configs\builder\twig;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use ReflectionClass;
use Slim\Views\Twig;

use Symfony\Bridge\Twig\Extension\AssetExtension;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;
use Symfony\WebpackEncoreBundle\Twig\EntryFilesTwigExtension;
use Twig\Error\LoaderError;
use Twig\Extension\DebugExtension;
use Twig\Extra\Intl\IntlExtension;

use App\Core\Config;
use App\Core\Enum\AppEnvironment;


use Twig\RuntimeLoader\FactoryRuntimeLoader;
use Symfony\Bridge\Twig\AppVariable;

readonly class Builder
{


    public function __construct(private Config $config, private ContainerInterface $container)
    {
    }

    /**
     * @return Twig
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws LoaderError
     */
    public function twig(): Twig {
        $config = $this->config;
        $container = $this->container;

        $appVariableReflection = new ReflectionClass(AppVariable::class);
        $vendorTwigBridgeDirectory = dirname($appVariableReflection->getFileName());
        $paths = [VIEW_PATH, $vendorTwigBridgeDirectory.'/Resources/views/Form'];
//            $t = microtime(true);
        //TODO навалить кешей для прода 0.001 секунды для 40 папок
        foreach (getPathsRecursively(APP_PATH, 'templates') as $p) {
            if (!in_array($p, $paths, true)) {
                $paths[] = $p;
            }
        }
//            dump(count($paths));
//            $t1 = microtime(true);
//            dump($t1-$t);
        $twig = Twig::create($paths, [
            'cache'       => STORAGE_PATH.'/cache/templates',
            'auto_reload' => AppEnvironment::isDevelopment($config->get('app_environment')),
            'debug'       => AppEnvironment::isDevelopment($config->get('app_environment')),
        ]);
        $translator = new Translator('en_En');
        $translator->addLoader('yaml', $container->get(YamlFileLoader::class));

        $twig->addExtension(new TranslationExtension($translator));
        $twig->addExtension(new IntlExtension());
        $twig->addExtension(new EntryFilesTwigExtension($container));
        $twig->addExtension(new AssetExtension($container->get('webpack_encore.packages')));
        $twig->addExtension(new FormExtension());
        $twig->addExtension(new StimulusExtension());
        $twig->addExtension(new DebugExtension());
        $formEngine = new TwigRendererEngine(
            $config->get('twig.default_form_theme', ['form_div_layout.html.twig']), $twig->getEnvironment()
        );
        $twig->addRuntimeLoader(
            new FactoryRuntimeLoader(
                [
                    FormRenderer::class => function () use ($formEngine, $container) {
                        return new FormRenderer($formEngine, $container->get(CsrfTokenManager::class));
                    }
                ]
            )
        );

        return $twig;
    }

}