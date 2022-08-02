<?php

declare(strict_types=1);

use Yiisoft\Yii\RateLimiter\CounterInterface;
use Yiisoft\Yii\RateLimiter\Storage\StorageInterface;
use Yiisoft\Yii\RateLimiter\Counter;
use Yiisoft\Cache\File\FileCache;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Yii\RateLimiter\Storage\SimpleCacheStorage;

/** @var array $params */

return [
    StorageInterface::class => function (Aliases $aliases) {
        $cache = new FileCache($aliases->get('@runtime/rate-limiter'));
        return new SimpleCacheStorage($cache);
    },
    CounterInterface::class => [
        'class' => Counter::class,
        '__construct()' => [
            'limit' => 7,
            'periodInSeconds' => 10,
        ],
    ],
];
