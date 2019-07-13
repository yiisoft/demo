<?php

use Psr\Log\LoggerInterface;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Cache\ArrayCache;
use Yiisoft\Cache\Cache;
use Yiisoft\Cache\CacheInterface;
use Yiisoft\Di\Container;
use Yiisoft\Factory\Definitions\Reference;
use Yiisoft\Log\FileRotator;

$params = $params ?? [];

return [
    'array-cache' => [
        '__class' => ArrayCache::class,
    ],
    CacheInterface::class => [
        '__class' => Cache::class,
        '__construct()' => [
            0 => Reference::to('array-cache'),
        ],
        'handler' => [
            '__class' => ArrayCache::class,
        ],
    ],
    'file-rotator' => [
        '__class' => FileRotator::class,
        '__construct()' => [
            10
        ]
    ],

    LoggerInterface::class => static function (Container $container) {
        $aliases = $container->get(\Yiisoft\Aliases\Aliases::class);

        $fileTarget = new Yiisoft\Log\FileTarget($aliases->get('@runtime/logs/app.log'), $container->get('file-rotator'));

        return new \Yiisoft\Log\Logger([
            'file' => $fileTarget->setCategories(['application']),
        ]);
    },

    Aliases::class => [
        '@root' => dirname(__DIR__),
        '@views' => '@root/views',
    ],
];
