<?php

declare(strict_types=1);

use Yiisoft\Definitions\ReferencesArray;
use Yiisoft\Yii\Debug\Debugger;

if (!(bool)($params['yiisoft/yii-debug']['enabled'] ?? false)) {
    return [];
}

return [
    Debugger::class => [
        '__construct()' => [
            'collectors' => ReferencesArray::from(
                array_merge(
                    $params['yiisoft/yii-debug']['collectors'],
                    $params['yiisoft/yii-debug']['collectors.console'] ?? []
                )
            ),
        ],
    ],
];
