<?php

return [
    'app' => [
        'name' => 'Yii Demo',
        'bootstrap' => ['debug' => 'debug'],
        'modules' => [
            'demo' => [
                '__class' => \Yiisoft\Yii\Demo\Module::class,
            ],
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
