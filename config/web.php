<?php

use App\Factory\ViewFactory;
use App\Repository\UserRepository;
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
use Yiisoft\EventDispatcher\Dispatcher;
use Yiisoft\EventDispatcher\Provider\Provider;
use Yiisoft\Factory\Definitions\Reference;
use Yiisoft\View\WebView;
use Yiisoft\Yii\Web\Emitter\EmitterInterface;
use Yiisoft\Yii\Web\Emitter\SapiEmitter;
use Yiisoft\Yii\Web\MiddlewareDispatcher;
use Yiisoft\Router\RouterInterface;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Router\UrlMatcherInterface;
use App\Factory\MiddlewareDispatcherFactory;
use App\Factory\AppRouterFactory;
use Yiisoft\Yii\Web\Session\Session;
use Yiisoft\Yii\Web\Session\SessionInterface;
use Yiisoft\Auth\IdentityRepositoryInterface;
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

    // custom stuff
    EmitterInterface::class => SapiEmitter::class,
    RouterInterface::class => new AppRouterFactory(),
    UrlMatcherInterface::class => Reference::to(RouterInterface::class),
    UrlGeneratorInterface::class => Reference::to(RouterInterface::class),
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

    // event dispatcher
    ListenerProviderInterface::class => Provider::class,
    EventDispatcherInterface::class => Dispatcher::class,

    // view
    WebView::class => new ViewFactory(),

    // user
    IdentityRepositoryInterface::class => UserRepository::class,
    User::class => static function (ContainerInterface $container) {
        $session = $container->get(SessionInterface::class);
        $identityRepository = $container->get(IdentityRepositoryInterface::class);
        $eventDispatcher = $container->get(EventDispatcherInterface::class);
        $user = new Yiisoft\Yii\Web\User\User($identityRepository, $eventDispatcher);
        $user->setSession($session);
        return $user;
    },
];
