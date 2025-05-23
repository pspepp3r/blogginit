<?php

declare(strict_types=1);

use ipinfo\ipinfo\IPinfo;
use Slim\App;
use DI\Container;
use Slim\Csrf\Guard;
use Slim\Views\Twig;
use Doctrine\ORM\ORMSetup;
use Slim\Factory\AppFactory;
use Doctrine\ORM\EntityManager;
use Src\Classes\Csrf;
use Src\Services\ConfigService;
use Doctrine\DBAL\DriverManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Twig\Extra\Intl\IntlExtension;
use Symfony\Component\Asset\Package;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Mailer\Transport;
use Slim\Interfaces\RouteParserInterface;
use Symfony\Bridge\Twig\Mime\BodyRenderer;
use Src\Classes\RouteEntityBindingStrategy;
use Src\Validators\RequestValidatorFactory;
use Symfony\Component\Mailer\MailerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Src\Classes\EntrypointLookupCollection as ClassesEntrypointLookupCollection;
use Symfony\Bridge\Twig\Extension\AssetExtension;
use Symfony\Component\Mime\BodyRendererInterface;
use Symfony\WebpackEncoreBundle\Asset\TagRenderer;
use Src\Contracts\RequestValidatorFactoryInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookup;
use Symfony\WebpackEncoreBundle\Twig\EntryFilesTwigExtension;
use Symfony\Component\Asset\VersionStrategy\JsonManifestVersionStrategy;

return [

    App::class => function (Container $container) {

        AppFactory::setContainer($container);

        $router = require CONFIG_PATH . '/routes/web.php';
        $addMiddleware = require CONFIG_PATH . '/middleware.php';

        $app = AppFactory::create();

        $app->getRouteCollector()->setDefaultInvocationStrategy(
            new RouteEntityBindingStrategy(
                $container->get(EntityManager::class),
                $app->getResponseFactory()
            )
        );

        $router($app);
        $addMiddleware($app);

        return $app;
    },

    BodyRendererInterface::class            => fn(Twig $twig) => new BodyRenderer($twig->getEnvironment()),

    ConfigService::class => new ConfigService(require CONFIG_PATH . '/app.php'),

    'csrf'                                  => fn(ResponseFactoryInterface $responseFactory, Csrf $csrf) => new Guard(
        $responseFactory,
        failureHandler: $csrf->failureHandler(),
        persistentTokenMode: true
    ),

    EntityManager::class => function ($connection, $ORMConfig, ConfigService $configService): EntityManager {

        $connection = DriverManager::getConnection($configService->get('db'));

        $ORMConfig = ORMSetup::createAttributeMetadataConfiguration([$configService->get('db.entity_dir')], $configService->get('db.dev_mode'));
        $entityManager = new EntityManager($connection, $ORMConfig);
        return $entityManager;
    },

    IPinfo::class => fn(ConfigService $config) => new IPinfo($config->get('ipinfo.access_token')),

    MailerInterface::class                  => function (ConfigService $config) {
        if ($config->get('mailer.driver') === 'log') {
            return new \Src\Classes\Mailer();
        }

        $transport = Transport::fromDsn($config->get('mailer.dsn'));

        return new Mailer($transport);
    },

    ResponseFactoryInterface::class => fn(App $app) => $app->getResponseFactory(),

    RequestValidatorFactoryInterface::class => fn(Container $container) => $container->get(
        RequestValidatorFactory::class
    ),

    RouteParserInterface::class             => fn(App $app) => $app->getRouteCollector()->getRouteParser(),

    SessionInterface::class => fn(ConfigService $config) => new Session(new NativeSessionStorage([
                'cookie_lifetime' => 2592000,
                'cookie_httponly' => $config->get('session.httponly'),
                'cookie_secure' => $config->get('session.secure'),
                'cookie_samesite' => $config->get('session.samesite')
            ])),


    Twig::class => function (Container $container, ConfigService $configService): Twig {

        $twig = Twig::create(VIEW_PATH, ['cache' => STORAGE_PATH . '/cache/twig', 'auto_reload' => $configService->get('db.dev_mode')]);

        $twig->addExtension(new IntlExtension());
        $twig->addExtension(new EntryFilesTwigExtension($container));
        $twig->addExtension(new AssetExtension($container->get('webpack_encore.packages')));

        return $twig;
    },

    'webpack_encore.entrypoint' => fn() => new EntrypointLookup(BUILD_PATH . '/entrypoints.json'),

    'webpack_encore.packages' => fn() => new Packages(
        new Package(new JsonManifestVersionStrategy(BUILD_PATH . '/manifest.json'))
    ),

    'webpack_encore.tag_renderer' => fn(Container $container) => new TagRenderer(
        new ClassesEntrypointLookupCollection($container, 'webpack_encore.entrypoint'),
        $container->get('webpack_encore.packages')
    ),

];
