<?php

declare(strict_types=1);

use App\Handler\NotFoundHandler;
use App\Middleware\LocaleMiddleware;
use Yiisoft\ErrorHandler\Middleware\ErrorCatcher;
use Yiisoft\Definitions\Reference;
use Yiisoft\Definitions\DynamicReference;
use Yiisoft\Injector\Injector;
use Yiisoft\Middleware\Dispatcher\MiddlewareDispatcher;
use Yiisoft\Router\Middleware\Router;
use Yiisoft\Session\SessionMiddleware;

return [
    Yiisoft\Yii\Web\Application::class => [
        '__construct()' => [
            'dispatcher' => DynamicReference::to(static function (Injector $injector) {
                return ($injector->make(MiddlewareDispatcher::class))
                    ->withMiddlewares(
                        [
                            Router::class,
                            SessionMiddleware::class,
                            ErrorCatcher::class,
                            LocaleMiddleware::class,
                        ]
                    );
            }),
            'fallbackHandler' => Reference::to(NotFoundHandler::class),
        ],
    ],
    LocaleMiddleware::class => [
        '__construct()' => [
            'locales' => $params['locales'],
        ],
    ],
];
