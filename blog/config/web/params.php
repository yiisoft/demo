<?php

declare(strict_types=1);

use Yiisoft\Cookies\CookieMiddleware;
use Yiisoft\ErrorHandler\Middleware\ErrorCatcher;
use Yiisoft\RequestProvider\RequestCatcherMiddleware;
use Yiisoft\Router\Middleware\Router;
use Yiisoft\Session\SessionMiddleware;
use Yiisoft\User\Login\Cookie\CookieLoginMiddleware;
use Yiisoft\Yii\Middleware\Locale;
use Yiisoft\Yii\Middleware\Subfolder;
use Yiisoft\Yii\Sentry\SentryMiddleware;

return [
    'middlewares' => [
        RequestCatcherMiddleware::class,
        ErrorCatcher::class,
        SentryMiddleware::class,
        SessionMiddleware::class,
        CookieMiddleware::class,
        CookieLoginMiddleware::class,
        Subfolder::class,
        Locale::class,
        Router::class,
    ],

    'locale' => [
        'locales' => ['en' => 'en-US', 'ru' => 'ru-RU', 'id' => 'id-ID', 'sk' => 'sk-SK', 'de' => 'de-DE'],
        'ignoredRequests' => [
            '/debug**',
            '/inspect**',
        ],
    ],
];
