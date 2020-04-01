<?php

use Yiisoft\Yii\Web\Data\DataResponseFormatterInterface;
use Yiisoft\Yii\Web\Data\Formatter\HtmlDataResponseFormatter;
use App\Factory\AppRouterFactory;
use App\Factory\MiddlewareDispatcherFactory;
use App\Factory\ViewFactory;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Yiisoft\Auth\IdentityRepositoryInterface;
use Yiisoft\EventDispatcher\Dispatcher\Dispatcher;
use Yiisoft\EventDispatcher\Provider\Provider;
use Yiisoft\Router\FastRoute\UrlGenerator;
use Yiisoft\Router\Group;
use Yiisoft\Router\RouteCollectorInterface;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Router\UrlMatcherInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\Web\Data\DataResponseFactoryInterface;
use Yiisoft\Yii\Web\Data\DataResponseFactory;
use Yiisoft\Yii\Web\MiddlewareDispatcher;
use Yiisoft\Yii\Web\Session\Session;
use Yiisoft\Yii\Web\Session\SessionInterface;
use Yiisoft\Yii\Web\User\User;

/**
 * @var array $params
 */

return [
    ContainerInterface::class => static function (ContainerInterface $container) {
        return $container;
    },

    // PSR-17 factories:
    RequestFactoryInterface::class => Psr17Factory::class,
    ServerRequestFactoryInterface::class => Psr17Factory::class,
    ResponseFactoryInterface::class => Psr17Factory::class,
    StreamFactoryInterface::class => Psr17Factory::class,
    UriFactoryInterface::class => Psr17Factory::class,
    UploadedFileFactoryInterface::class => Psr17Factory::class,
    DataResponseFormatterInterface::class => HtmlDataResponseFormatter::class,
    DataResponseFactoryInterface::class => DataResponseFactory::class,

    // Router:
    RouteCollectorInterface::class => Group::create(),
    UrlMatcherInterface::class => new AppRouterFactory(),
    UrlGeneratorInterface::class => UrlGenerator::class,

    MiddlewareDispatcher::class => new MiddlewareDispatcherFactory(),
    SessionInterface::class => [
        '__class' => Session::class,
        '__construct()' => [
            $params['session']['options'] ?? [],
            $params['session']['handler'] ?? null,
        ],
    ],

    // here you can configure custom prefix of the web path
    // \Yiisoft\Yii\Web\Middleware\SubFolder::class => [
    //     'prefix' => '',
    // ],

    // Event dispatcher:
    ListenerProviderInterface::class => Provider::class,
    EventDispatcherInterface::class => Dispatcher::class,

    // View:
    WebView::class => new ViewFactory(),

    // User:
    IdentityRepositoryInterface::class => static function (ContainerInterface $container) {
        return $container->get(\Cycle\ORM\ORMInterface::class)->getRepository(\App\Entity\User::class);
    },
    User::class => static function (ContainerInterface $container) {
        $session = $container->get(SessionInterface::class);
        $identityRepository = $container->get(IdentityRepositoryInterface::class);
        $eventDispatcher = $container->get(EventDispatcherInterface::class);
        $user = new Yiisoft\Yii\Web\User\User($identityRepository, $eventDispatcher);
        $user->setSession($session);

        return $user;
    },
];
