<?php

use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Cache\ArrayCache;
use Yiisoft\Cache\Cache;
use Yiisoft\Cache\CacheInterface;
use Yiisoft\Log\FileRotator;
use Yiisoft\Log\FileRotatorInterface;

$params = $params ?? [];

return [
    Aliases::class => [
        '@root' => dirname(__DIR__),
        '@views' => '@root/views',
    ],
    CacheInterface::class => [
        '__class' => Cache::class,
        'handler' => [
            '__class' => ArrayCache::class,
        ],
    ],
    FileRotatorInterface::class => [
        '__class' => FileRotator::class,
        '__construct()' => [
            10
        ]
    ],
];
