<?php

declare(strict_types=1);

use Yiisoft\Swagger\Middleware\SwaggerUI;

return [
    SwaggerUI::class => [
        '__construct()' => [
            'params' => $params['yiisoft/yii-swagger']['ui-params'],
        ],
    ],
];

