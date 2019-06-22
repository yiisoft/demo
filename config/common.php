<?php

$params = $params ?? [];

return [
    'array-cache' => [
        '__class' => \Yiisoft\Cache\ArrayCache::class,
    ],
    'cache' => [
        '__class' => \Yiisoft\Cache\Cache::class,
        '__construct()' => [
            0 => \yii\di\Reference::to('array-cache'),
        ],
        'handler' => [
            '__class' => \Yiisoft\Cache\ArrayCache::class,
        ],
    ],
    'db' => [
        '__class'   => \Yiisoft\Db\Connection::class,
        'dsn'       => 'sqlite:dbname=' . $params['db.name']
            . (!empty($params['db.host']) ? (';host=' . $params['db.host']) : '')
            . (!empty($params['db.port']) ? (';port=' . $params['db.port']) : ''),
        'username'  => $params['db.user'],
        'password'  => $params['db.password'],
    ],
    'file-rotator' => [
        '__class' => \Yiisoft\Log\FileRotator::class,
        '__construct()' => [
            10
        ]
    ],
    'logger' => static function (\yii\di\Container $container) {
        /** @var \yii\base\Aliases $aliases */
        $aliases = $container->get('aliases');

        $fileTarget = new Yiisoft\Log\FileTarget($aliases->get('@runtime/logs/app.log'),  $container->get('file-rotator'));

        return new \Yiisoft\Log\Logger([
            'file' => $fileTarget->setCategories(['application']),
        ]);
    },
];
