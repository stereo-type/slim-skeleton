<?php

declare(strict_types=1);

use App\Core\Auth;
use App\Core\Config;
use App\Core\Contracts\EntityManagerServiceInterface;
use App\Core\Contracts\RequestValidatorFactoryInterface;
use App\Core\Contracts\SessionInterface;
use App\Core\Contracts\User\AuthInterface;
use App\Core\Contracts\User\UserProviderServiceInterface;
use App\Core\Csrf;
use App\Core\DataObjects\SessionConfig;
use App\Core\Enum\AppEnvironment;
use App\Core\Enum\SameSite;
use App\Core\Enum\StorageDriver;
use App\Core\Filters\UserFilter;
use App\Core\RequestValidators\RequestValidatorFactory;
use App\Core\RouteEntityBindingStrategy;
use App\Core\Services\EntityManagerService;
use App\Core\Services\UserProviderService;
use App\Core\Session;
use Clockwork\Clockwork;
use Clockwork\DataSource\DoctrineDataSource;
use Clockwork\Storage\FileStorage;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;
use DoctrineExtensions\Query\Mysql\DateFormat;
use DoctrineExtensions\Query\Mysql\Month;
use DoctrineExtensions\Query\Mysql\Year;
use League\Flysystem\Filesystem;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\SimpleCache\CacheInterface;
use Slim\App;
use Slim\Csrf\Guard;
use Slim\Factory\AppFactory;
use Slim\Interfaces\RouteParserInterface;
use Slim\Views\Twig;
use Symfony\Bridge\Twig\Extension\AssetExtension;
use Symfony\Bridge\Twig\Mime\BodyRenderer;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Asset\VersionStrategy\JsonManifestVersionStrategy;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Psr16Cache;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\BodyRendererInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\Storage\CacheStorage;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookup;
use Symfony\WebpackEncoreBundle\Asset\TagRenderer;
use Symfony\WebpackEncoreBundle\Twig\EntryFilesTwigExtension;
use Twig\Extra\Intl\IntlExtension;

use function DI\create;

/**Если есть в проекте конфиг подгружаем его иначе берем из ядра*/
$config_file = file_exists(CONFIG_PATH.'/config.php')
    ? CONFIG_PATH.'/config.php' : CORE_CONFIG_PATH.'/core_config.php';

$route_file = file_exists(CONFIG_PATH.'/routes/web.php')
    ? CONFIG_PATH.'/routes/web.php' : CORE_CONFIG_PATH.'/routes/web.php';

$middleware_file = file_exists(CONFIG_PATH.'/middleware.php')
    ? CONFIG_PATH.'/middleware.php' : CORE_CONFIG_PATH.'/middleware.php';


return [
    App::class                              =>
        static function (ContainerInterface $container) use ($middleware_file, $route_file) {
            AppFactory::setContainer($container);
            $addMiddlewares = require $middleware_file;
            $router = require $route_file;
            $app = AppFactory::create();
            $app->getRouteCollector()->setDefaultInvocationStrategy(
                new RouteEntityBindingStrategy(
                    $container->get(EntityManagerServiceInterface::class),
                    $app->getResponseFactory()
                )
            );
            $router($app);
            $addMiddlewares($app);
            return $app;
        },
    Config::class                           => create(Config::class)->constructor(require $config_file),
    EntityManagerInterface::class           =>
        static function (Config $config) {
            $ormConfig = ORMSetup::createAttributeMetadataConfiguration(
                $config->get('doctrine.entity_dir'),
                $config->get('doctrine.dev_mode')
            );

            $ormConfig->addFilter('user', UserFilter::class);

            if (class_exists('DoctrineExtensions\Query\Mysql\Year')) {
                $ormConfig->addCustomDatetimeFunction('YEAR', Year::class);
            }

            if (class_exists('DoctrineExtensions\Query\Mysql\Month')) {
                $ormConfig->addCustomDatetimeFunction('MONTH', Month::class);
            }

            if (class_exists('DoctrineExtensions\Query\Mysql\DateFormat')) {
                $ormConfig->addCustomStringFunction('DATE_FORMAT', DateFormat::class);
            }

            return new EntityManager(
                DriverManager::getConnection($config->get('doctrine.connection'), $ormConfig),
                $ormConfig
            );
        },
    Twig::class                             =>
        static function (Config $config, ContainerInterface $container) {

            $twig = Twig::create([
                CORE_VIEW_PATH,
                VIEW_PATH
            ], [
                'cache'       => STORAGE_PATH.'/cache/templates',
                'auto_reload' => AppEnvironment::isDevelopment($config->get('app_environment')),
            ]);

            $twig->addExtension(new IntlExtension());
            $twig->addExtension(new EntryFilesTwigExtension($container));
            $twig->addExtension(new AssetExtension($container->get('webpack_encore.packages')));

            return $twig;
        },
    /**
     * The following two bindings are needed for EntryFilesTwigExtension & AssetExtension to work for Twig
     */
    'webpack_encore.packages'               =>
        static fn() => new Packages(new Package(new JsonManifestVersionStrategy(BUILD_PATH.'/manifest.json'))),
    'webpack_encore.tag_renderer'           =>
        static fn(ContainerInterface $container) => new TagRenderer(
            new EntrypointLookup(BUILD_PATH.'/entrypoints.json'),
            $container->get('webpack_encore.packages')
        ),
    ResponseFactoryInterface::class         => static fn(App $app) => $app->getResponseFactory(),
    AuthInterface::class                    =>
        static fn(ContainerInterface $container) => $container->get(Auth::class),
    UserProviderServiceInterface::class     =>
        static fn(ContainerInterface $container) => $container->get(UserProviderService::class),
    SessionInterface::class                 =>
        static fn(Config $config) => new Session(
            new SessionConfig(
                $config->get('session.name', ''),
                $config->get('session.flash_name', 'flash'),
                $config->get('session.secure', true),
                $config->get('session.httponly', true),
                SameSite::from($config->get('session.samesite', 'lax'))
            )
        ),
    RequestValidatorFactoryInterface::class =>
        static fn(ContainerInterface $container) => $container->get(RequestValidatorFactory::class),
    'csrf'                                  =>
        static function (ResponseFactoryInterface $responseFactory, Csrf $csrf) {
            return new Guard($responseFactory, failureHandler: $csrf->failureHandler(), persistentTokenMode: true);
        },
    Filesystem::class                       =>
        static function (Config $config) {
            $adapter = match ($config->get('storage.driver')) {
                StorageDriver::Local => new League\Flysystem\Local\LocalFilesystemAdapter(STORAGE_PATH),
            };
            return new League\Flysystem\Filesystem($adapter);
        },
    Clockwork::class                        =>
        static function (EntityManagerInterface $entityManager) {
            $clockwork = new Clockwork();
            $clockwork->storage(new FileStorage(STORAGE_PATH.'/clockwork'));
            $clockwork->addDataSource(new DoctrineDataSource($entityManager));
            return $clockwork;
        },
    EntityManagerServiceInterface::class    =>
        static fn(EntityManagerInterface $entityManager) => new EntityManagerService($entityManager),
    MailerInterface::class                  =>
        static function (Config $config) {
            if ($config->get('mailer.driver') === 'log') {
                return new \App\Core\Mailer();
            }
            $transport = Transport::fromDsn($config->get('mailer.dsn'));
            return new Mailer($transport);
        },
    BodyRendererInterface::class            => static fn(Twig $twig) => new BodyRenderer($twig->getEnvironment()),
    RouteParserInterface::class             => static fn(App $app) => $app->getRouteCollector()->getRouteParser(),
    CacheInterface::class                   => static fn(RedisAdapter $redisAdapter) => new Psr16Cache($redisAdapter),
    RedisAdapter::class                     =>
        static function (Config $config) {
            $redis = new Redis();
            $config = $config->get('redis');
            $redis->connect($config['host'], (int)$config['port']);
            if ($config['password']) {
                $redis->auth($config['password']);
            }
            return new RedisAdapter($redis);
        },
    RateLimiterFactory::class               =>
        static fn(RedisAdapter $redisAdapter, Config $config) => new RateLimiterFactory(
            $config->get('limiter'), new CacheStorage($redisAdapter)
        ),
];
