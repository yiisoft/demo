<?php

use yii\base\Aliases;
use yii\di\Container;
use Yiisoft\Cache\ArrayCache;
use Yiisoft\Cache\Cache;
use Yiisoft\Db\Connection;
use Yiisoft\Factory\Definitions\Reference;
use Yiisoft\Log\FileRotator;
use Yiisoft\Log\Logger;

$params = $params ?? [];

return [
    'array-cache' => [
        '__class' => ArrayCache::class,
    ],
    'cache' => [
        '__class' => Cache::class,
        '__construct()' => [
            0 => Reference::to('array-cache'),
        ],
        'handler' => [
            '__class' => ArrayCache::class,
        ],
    ],
    'db' => [
        '__class'   => Connection::class,
        'dsn'       => 'sqlite:dbname=' . $params['db.name']
            . (!empty($params['db.host']) ? (';host=' . $params['db.host']) : '')
            . (!empty($params['db.port']) ? (';port=' . $params['db.port']) : ''),
        'username'  => $params['db.user'],
        'password'  => $params['db.password'],
    ],
    'file-rotator' => [
        '__class' => FileRotator::class,
        '__construct()' => [
            10
        ]
    ],
    'logger' => static function (Container $container) {
        /** @var Aliases $aliases */
        $aliases = $container->get('aliases');

        $fileTarget = new Yiisoft\Log\FileTarget($aliases->get('@runtime/logs/app.log'),  $container->get('file-rotator'));

        return new Logger([
            'file' => $fileTarget->setCategories(['application']),
        ]);
    },
];
