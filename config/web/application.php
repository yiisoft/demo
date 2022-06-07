<?php

declare(strict_types=1);

use App\Handler\NotFoundHandler;
use App\Middleware\LocaleMiddleware;
use Yiisoft\Definitions\DynamicReference;
use Yiisoft\Definitions\Reference;
use Yiisoft\Middleware\Dispatcher\MiddlewareDispatcher;

/** @var array $params */

return [
    Yiisoft\Yii\Http\Application::class => [
        '__construct()' => [
            'dispatcher' => DynamicReference::to([
                'class' => MiddlewareDispatcher::class,
                'withMiddlewares()' => [$params['middlewares']],
            ]),
            'fallbackHandler' => Reference::to(NotFoundHandler::class),
        ],
    ],
    LocaleMiddleware::class => [
        '__construct()' => [
            'locales' => $params['locales'],
        ],
    ],
];
