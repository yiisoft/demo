<?php

declare(strict_types=1);

use App\Handler\NotFoundHandler;
use Yiisoft\ErrorHandler\Middleware\ErrorCatcher;
use Yiisoft\Factory\Definition\Reference;
use Yiisoft\Factory\Definition\DynamicReference;
use Yiisoft\Injector\Injector;
use Yiisoft\Middleware\Dispatcher\MiddlewareDispatcher;
use Yiisoft\Router\Middleware\Router;
use Yiisoft\Session\SessionMiddleware;

return [
    'app.dispatcher' => static function (Injector $injector) {
        return ($injector->make(MiddlewareDispatcher::class))
            ->withMiddlewares(
                [
                    Router::class,
                    SessionMiddleware::class,
                    ErrorCatcher::class,
                ]
            );
    },
    Yiisoft\Yii\Web\Application::class => [
        '__construct()' => [
            'dispatcher' => Reference::to('app.dispatcher'),
            'fallbackHandler' => Reference::to(NotFoundHandler::class),
        ],
    ],
];
