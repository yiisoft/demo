<?php

return [
    'app' => [
        'name' => 'Yii Demo',
        'bootstrap' => ['debug' => 'debug'],
        'controllerNamespace' => \Yiisoft\Yii\Demo\Controllers::class,
        'aliases' => [
            '@webroot'  => dirname(__DIR__) . '/public',
            '@doc'      => dirname(__DIR__) . '/docs',
            '@npm'      => dirname(__DIR__) . '/node_modules',
        ],
    ],
    'assetManager' => [
        'appendTimestamp' => true,
        'linkAssets' => true,
        'bundles' => [
        \Yiisoft\Yii\Bootstrap4\BootstrapAsset::class => [
                'css' => [],
            ]
        ]
    ],
    'urlManager' => [
        'enablePrettyUrl' => true,
        'showScriptName' => false,
        'rules' => [
            'site/packages/<package:[-\w]+>' => 'site/package',
        ],
    ],
];
