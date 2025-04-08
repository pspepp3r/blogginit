<?php

declare(strict_types=1);

use Slim\App;
use DI\Container;
use Slim\Views\Twig;
use Doctrine\ORM\ORMSetup;
use Slim\Factory\AppFactory;
use Doctrine\ORM\EntityManager;
use Src\Services\ConfigService;
use Doctrine\DBAL\DriverManager;
use Twig\Extra\Intl\IntlExtension;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\Packages;
use Src\Classes\RouteEntityBindingStrategy;
use Psr\Http\Message\ResponseFactoryInterface;
use Symfony\Bridge\Twig\Extension\AssetExtension;
use Src\RequestValidators\RequestValidatorFactory;
use Symfony\WebpackEncoreBundle\Asset\TagRenderer;
use Src\Contracts\RequestValidatorFactoryInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookup;
use Symfony\WebpackEncoreBundle\Twig\EntryFilesTwigExtension;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookupCollection;
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

    ConfigService::class => new ConfigService(require CONFIG_PATH . '/app.php'),

    EntityManager::class => function ($connection, $ORMConfig, ConfigService $configService): EntityManager {

        $connection = DriverManager::getConnection($configService->get('db'));

        $ORMConfig = ORMSetup::createAttributeMetadataConfiguration([$configService->get('db.entity_dir')], $configService->get('db.dev_mode'));
        $entityManager = new EntityManager($connection, $ORMConfig);
        return $entityManager;
    },

    ResponseFactoryInterface::class => fn(App $app) => $app->getResponseFactory(),

    RequestValidatorFactoryInterface::class => fn(Container $container) => $container->get(
        RequestValidatorFactory::class
    ),

    SessionInterface::class => fn(Container $container) => $container->get(Session::class),

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
        new EntrypointLookupCollection($container, 'webpack_encore.entrypoint'),
        $container->get('webpack_encore.packages')
    ),

];
