<?php

declare(strict_types=1);

use App\Provider\CacheProvider;
use App\Provider\LoggerProvider;
use App\Provider\WebViewProvider;
use App\Provider\RepositoryProvider;
use Yiisoft\Arrays\Modifier\ReverseBlockMerge;

return [
    'cacheProvider' => CacheProvider::class,
    'loggerProvider' => LoggerProvider::class,
    'webViewProvider' => WebViewProvider::class,
    'RepositoryProvider' => RepositoryProvider::class,
    ReverseBlockMerge::class => new ReverseBlockMerge(),
];
