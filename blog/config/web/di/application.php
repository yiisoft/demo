<?php

declare(strict_types=1);

use App\Handler\NotFoundHandler;
use Yiisoft\Definitions\DynamicReference;
use Yiisoft\Definitions\Reference;
use Yiisoft\Middleware\Dispatcher\MiddlewareDispatcher;
use Yiisoft\Yii\Middleware\Locale;
use Yiisoft\Yii\Middleware\Subfolder;

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
    Locale::class => [
        '__construct()' => [
            'supportedLocales' => $params['locale']['locales'],
            'ignoredRequestUrlPatterns' => $params['locale']['ignoredRequests'],
        ],
    ],
    Subfolder::class => [
        '__construct()' => [
            'prefix' => !empty(trim($_ENV['BASE_URL'] ?? '', '/')) ? $_ENV['BASE_URL'] : null,
        ],
    ],
];
