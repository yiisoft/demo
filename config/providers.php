<?php

declare(strict_types=1);

use App\Provider\CacheProvider;
use App\Provider\EventDispatcherProvider;
use App\Provider\LoggerProvider;
use App\Provider\WebViewProvider;

return [
    'cacheProvider' => CacheProvider::class,
    'eventDispatcherProvider' => EventDispatcherProvider::class,
    'loggerProvider' => LoggerProvider::class,
    'webViewProvider' => WebViewProvider::class
];
