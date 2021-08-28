<?php

declare(strict_types=1);

// Do not edit. Content will be replaced.
return [
    '/' => [
        'bootstrap' => [
            '/' => [
                'config/shared/bootstrap.php',
            ],
            'yiisoft/widget' => [
                'bootstrap.php',
            ],
        ],
        'bootstrap-web' => [
            '/' => [
                'config/shared/bootstrap-web.php',
            ],
        ],
        'common' => [
            'yiisoft/cache-file' => [
                'common.php',
            ],
            'yiisoft/log-target-file' => [
                'common.php',
            ],
            'yiisoft/mailer-symfony' => [
                'common.php',
            ],
            'yiisoft/router-fastroute' => [
                'common.php',
            ],
            'yiisoft/yii-cycle' => [
                'common.php',
            ],
            'yiisoft/yii-event' => [
                'common.php',
            ],
            'yiisoft/yii-debug' => [
                'common.php',
            ],
            'yiisoft/profiler' => [
                'common.php',
            ],
            'yiisoft/yii-filesystem' => [
                'common.php',
            ],
            'yiisoft/aliases' => [
                'common.php',
            ],
            'yiisoft/cache' => [
                'common.php',
            ],
            'yiisoft/validator' => [
                'common.php',
            ],
            'yiisoft/view' => [
                'common.php',
            ],
            'yiisoft/router' => [
                'common.php',
            ],
        ],
        'console' => [
            'yiisoft/yii-cycle' => [
                'console.php',
            ],
            'yiisoft/yii-console' => [
                'console.php',
            ],
            'yiisoft/yii-event' => [
                'console.php',
            ],
            'yiisoft/yii-debug' => [
                'console.php',
            ],
        ],
        'definitions' => [
            '/' => [
                '$common',
                'config/shared/definitions/*.php',
            ],
        ],
        'definitions-web' => [
            '/' => [
                '$web',
                'config/shared/definitions-web/*.php',
            ],
        ],
        'events' => [
            '/' => [
                'config/shared/events.php',
            ],
            'yiisoft/yii-event' => [
                'events.php',
            ],
        ],
        'events-console' => [
            'yiisoft/yii-cycle' => [
                'events-console.php',
            ],
            'yiisoft/log' => [
                'events-console.php',
            ],
            'yiisoft/yii-console' => [
                'events-console.php',
            ],
            'yiisoft/yii-event' => [
                '$events',
                'events-console.php',
            ],
            'yiisoft/yii-debug' => [
                'events-console.php',
            ],
        ],
        'events-web' => [
            '/' => [
                'config/shared/events-web.php',
            ],
            'yiisoft/log' => [
                'events-web.php',
            ],
            'yiisoft/yii-event' => [
                '$events',
                'events-web.php',
            ],
            'yiisoft/yii-debug' => [
                'events-web.php',
            ],
            'yiisoft/profiler' => [
                'events-web.php',
            ],
        ],
        'params' => [
            '/' => [
                'config/shared/params.php',
                '?config/shared/params-local.php',
            ],
            'yiisoft/cache-file' => [
                'params.php',
            ],
            'yiisoft/log-target-file' => [
                'params.php',
            ],
            'yiisoft/mailer-symfony' => [
                'params.php',
            ],
            'yiisoft/router-fastroute' => [
                'params.php',
            ],
            'yiisoft/user' => [
                'params.php',
            ],
            'yiisoft/yii-bootstrap5' => [
                'params.php',
            ],
            'yiisoft/yii-cycle' => [
                'params.php',
            ],
            'yiisoft/yii-debug-api' => [
                'params.php',
            ],
            'yiisoft/yii-debug-viewer' => [
                'config/params.php',
            ],
            'yiisoft/assets' => [
                'params.php',
            ],
            'yiisoft/session' => [
                'params.php',
            ],
            'yiisoft/yii-console' => [
                'params.php',
            ],
            'yiisoft/yii-debug' => [
                'params.php',
            ],
            'yiisoft/profiler' => [
                'params.php',
            ],
            'yiisoft/yii-view' => [
                'params.php',
            ],
            'yiisoft/aliases' => [
                'params.php',
            ],
            'yiisoft/csrf' => [
                'params.php',
            ],
            'yiisoft/view' => [
                'params.php',
            ],
        ],
        'providers' => [
            '/' => [
                'config/shared/providers.php',
            ],
            'yiisoft/yii-debug' => [
                'providers.php',
            ],
            'yiisoft/yii-filesystem' => [
                'providers.php',
            ],
        ],
        'providers-console' => [
            'yiisoft/yii-console' => [
                'providers-console.php',
            ],
        ],
        'providers-web' => [
            '/' => [
                'config/shared/providers-web.php',
            ],
            'yiisoft/yii-cycle' => [
                'providers-web.php',
            ],
            'yiisoft/yii-debug-api' => [
                'providers-web.php',
            ],
        ],
        'routes' => [
            'yiisoft/yii-debug-api' => [
                'routes.php',
            ],
            'yiisoft/yii-debug-viewer' => [
                'config/routes.php',
            ],
        ],
        'tests' => [
            'yiisoft/yii-debug' => [
                'tests.php',
            ],
        ],
        'web' => [
            'yiisoft/error-handler' => [
                'web.php',
            ],
            'yiisoft/router-fastroute' => [
                'web.php',
            ],
            'yiisoft/user' => [
                'web.php',
            ],
            'yiisoft/yii-bootstrap5' => [
                'web/*.php',
            ],
            'yiisoft/yii-debug-api' => [
                'web.php',
            ],
            'yiisoft/yii-debug-viewer' => [
                'config/web.php',
            ],
            'yiisoft/assets' => [
                'web.php',
            ],
            'yiisoft/session' => [
                'web.php',
            ],
            'yiisoft/yii-event' => [
                'web.php',
            ],
            'yiisoft/yii-debug' => [
                'web.php',
            ],
            'yiisoft/yii-view' => [
                'web.php',
            ],
            'yiisoft/csrf' => [
                'web.php',
            ],
            'yiisoft/data-response' => [
                'web.php',
            ],
            'yiisoft/view' => [
                'web.php',
            ],
            'yiisoft/middleware-dispatcher' => [
                'web.php',
            ],
        ],
    ],
    'application' => [
        'bootstrap' => [
            '/' => [
                '$bootstrap-web',
                'config/application/bootstrap.php',
            ],
        ],
        'definitions' => [
            '/' => [
                '$definitions-web',
            ],
        ],
        'events' => [
            '/' => [
                '$events-web',
            ],
        ],
        'providers' => [
            '/' => [
                '$providers-web',
            ],
        ],
        'routes' => [
            '/' => [
                'config/application/routes-backend.php',
                'config/application/routes.php',
            ],
        ],
    ],
    'console' => [
        'bootstrap' => [
            '/' => [
                'config/console/bootstrap.php',
            ],
        ],
        'definitions' => [
            '/' => [
                '$console',
            ],
        ],
        'events' => [
            '/' => [
                '$events-console',
                'config/console/events.php',
            ],
        ],
        'providers' => [
            '/' => [
                '$providers-console',
                'config/console/providers.php',
            ],
        ],
    ],
];
