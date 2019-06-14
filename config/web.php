<?php
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use yii\di\Reference;
use Yiisoft\Web\Emitter\EmitterInterface;
use Yiisoft\Web\Emitter\SapiEmitter;
use Yiisoft\Web\MiddlewareDispatcher;
use Yiisoft\Router\RouterInterface;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Router\UrlMatcherInterface;
use Yiisoft\Yii\Demo\Factory\MiddlewareDispatcherFactory;
use Yiisoft\Yii\Demo\Factory\AppRouterFactory;

$basePath = dirname(__DIR__, 2);

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
    \Psr\EventDispatcher\ListenerProviderInterface::class => \Yiisoft\EventDispatcher\Provider\Provider::class,
    \Psr\EventDispatcher\EventDispatcherInterface::class => \Yiisoft\EventDispatcher\Dispatcher::class,

    // view
    \Yiisoft\View\WebView::class => new \Yiisoft\Yii\Demo\Factory\ViewFactory($basePath . '/views'),

    'app' => [
        'name' => 'Yii Demo',
        'bootstrap' => ['debug' => 'debug'],
        'modules' => [
            'demo' => [
                '__class' => \Yiisoft\Yii\Demo\Module::class,
            ],
        ],
    ],
    'assetManager' => [
        'appendTimestamp' => true,
        'linkAssets' => true,
        'bundles' => [
        \Yiisoft\Yii\Bootstrap4\BootstrapAsset::class => [
                'css' => [],
            ]
        ]
    ],
    'urlManager' => [
        'enablePrettyUrl' => true,
        'showScriptName' => false,
        'rules' => [
            'site/packages/<package:[-\w]+>' => 'site/package',
        ],
    ],
];
