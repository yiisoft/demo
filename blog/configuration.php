<?php

declare(strict_types=1);

return [
    'config-plugin' => [
        'params' => [
            'common/params.php',
        ],
        'params-web' => [
            '$params',
            'web/params.php',
        ],
        'params-console' => [
            '$params',
            'console/params.php',
        ],
        'di' => 'common/di/*.php',
        'di-web' => [
            '$di',
            'web/di/*.php',
        ],
        'di-console' => '$di',
        'di-providers' => [],
        'di-providers-web' => [
            '$di-providers',
        ],
        'di-providers-console' => [
            '$di-providers',
        ],
        'di-delegates' => [],
        'di-delegates-web' => [
            '$di-delegates',
        ],
        'di-delegates-console' => [
            '$di-delegates',
        ],
        'events' => [],
        'events-web' => [
            '$events',
            'web/events.php',
        ],
        'events-console' => [
            '$events',
            'console/events.php',
        ],
        'app-routes' => [
            'common/routes/*.php',
        ],
        'bootstrap' => [
            'common/bootstrap.php',
        ],
        'bootstrap-web' => [
            '$bootstrap',
        ],
        'bootstrap-console' => [
            '$bootstrap',
        ],
    ],
    'config-plugin-environments' => [
        'dev' => [
            'params' => [
                'environments/dev/params.php',
            ],
        ],
        'prod' => [
            'params' => [
                'environments/prod/params.php',
            ],
        ],
        'test' => [
            'params' => [
                'environments/test/params.php',
            ],
        ],
    ],
    'config-plugin-options' => [
        'source-directory' => 'config',
    ],
];
