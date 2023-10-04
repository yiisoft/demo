<?php

declare(strict_types=1);

use Yiisoft\Aliases\Aliases;
use Yiisoft\Cache\File\FileCache;
use Yiisoft\Yii\RateLimiter\Counter;
use Yiisoft\Yii\RateLimiter\CounterInterface;
use Yiisoft\Yii\RateLimiter\Storage\SimpleCacheStorage;
use Yiisoft\Yii\RateLimiter\Storage\StorageInterface;

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
