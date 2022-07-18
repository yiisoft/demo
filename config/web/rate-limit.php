<?php

declare(strict_types=1);

use Yiisoft\Yii\RateLimiter\CounterInterface;
use Yiisoft\Yii\RateLimiter\Storage\StorageInterface;
use \Yiisoft\Yii\RateLimiter\Counter;

/** @var array $params */

return [
    StorageInterface::class => function () {
        $cache = new \Yiisoft\Cache\File\FileCache(__DIR__ . '/../runtime/rate-limit/');
        return new \Yiisoft\Yii\RateLimiter\Storage\SimpleCacheStorage($cache);
    },
    CounterInterface::class => [
        'class' => Counter::class,
        '__construct()' => [
            'limit' => 3,
            'periodInSeconds' => 10,
        ],
    ],
];
