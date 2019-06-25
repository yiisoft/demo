<?php

use App\Factory\ViewFactory;
use Nyholm\Psr7\Factory\Psr17Factory;
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

$basePath = dirname(__DIR__);

return [
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

    // event dispatcher
    ListenerProviderInterface::class => Provider::class,
    EventDispatcherInterface::class => Dispatcher::class,

    // view
    WebView::class => new ViewFactory($basePath . '/views'),
];
