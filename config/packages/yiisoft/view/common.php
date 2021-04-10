<?php

declare(strict_types=1);

use Yiisoft\Aliases\Aliases;
use Yiisoft\View\View;

/** @var array $params */

return [
    View::class => [
        'class' => View::class,
        'constructor' => [
            'basePath' => static fn (Aliases $aliases) => $aliases->get($params['yiisoft/view']['basePath']),
        ],
        'callMethods' => [
            'setDefaultParameters' => [
                $params['yiisoft/view']['defaultParameters'],
            ],
        ],
    ],
];
