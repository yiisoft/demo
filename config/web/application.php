<?php

declare(strict_types=1);

use App\Handler\NotFoundHandler;
use App\Middleware\LocaleMiddleware;
use Yiisoft\Definitions\DynamicReference;
use Yiisoft\Definitions\Reference;
use Yiisoft\Injector\Injector;
use Yiisoft\Middleware\Dispatcher\MiddlewareDispatcher;

return [
    Yiisoft\Yii\Web\Application::class => [
        '__construct()' => [
            'dispatcher' => DynamicReference::to(static function (Injector $injector) use ($params) {
                return ($injector->make(MiddlewareDispatcher::class))
                    ->withMiddlewares(array_reverse($params['middlewares']));
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
