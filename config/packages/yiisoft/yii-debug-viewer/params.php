<?php

declare(strict_types=1);

return [
    'yiisoft/yii-debug-viewer' => [
        'targetHost' => '/',
        'panels' => [
            'panel-info' => [
                'name' => 'Info',
                'route' => '/debug/panels/info',
            ],
            'panel-request' => [
                'name' => 'Request',
                'route' => '/debug/panels/request',
            ],
            'panel-routes' => [
                'name' => 'Routes',
                'route' => '/debug/panels/routes',
            ],
            'panel-logs' => [
                'name' => 'Logs',
                'route' => '/debug/panels/logs',
            ],
            'panel-events' => [
                'name' => 'Events',
                'route' => '/debug/panels/events',
            ],
            'panel-services' => [
                'name' => 'Services',
                'route' => '/debug/panels/services',
            ],
            'panel-middlewares' => [
                'name' => 'Middlewares',
                'route' => '/debug/panels/middlewares',
            ],
        ],
    ],
];
