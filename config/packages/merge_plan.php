<?php

declare(strict_types=1);

// Do not edit. Content will be replaced.
return [
    'common' => [
        '/' => [
            'config/common/*.php',
        ],
        'yiisoft/cache' => [
            'config/common.php',
        ],
        'yiisoft/yii-debug' => [
            'config/common.php',
        ],
        'yiisoft/yii-filesystem' => [
            'config/common.php',
        ],
        'yiisoft/yii-cycle' => [
            'config/common.php',
        ],
        'yiisoft/yii-event' => [
            'config/common.php',
        ],
        'yiisoft/aliases' => [
            'config/common.php',
        ],
        'yiisoft/router-fastroute' => [
            'config/common.php',
        ],
        'yiisoft/router' => [
            'config/common.php',
        ],
        'yiisoft/profiler' => [
            'config/common.php',
        ],
        'yiisoft/mailer-swiftmailer' => [
            'config/common.php',
        ],
        'yiisoft/view' => [
            'config/common.php',
        ],
        'yiisoft/log-target-file' => [
            'config/common.php',
        ],
        'yiisoft/validator' => [
            'config/common.php',
        ],
        'yiisoft/cache-file' => [
            'config/common.php',
        ],
    ],
    'params' => [
        '/' => [
            'config/params.php',
            '?config/params-local.php',
        ],
        'yiisoft/yii-view' => [
            'config/params.php',
        ],
        'yiisoft/csrf' => [
            'config/params.php',
        ],
        'yiisoft/yii-debug-api' => [
            'config/params.php',
        ],
        'yiisoft/yii-debug' => [
            'config/params.php',
        ],
        'yiisoft/yii-cycle' => [
            'config/params.php',
        ],
        'yiisoft/yii-console' => [
            'config/params.php',
        ],
        'yiisoft/yii-bootstrap5' => [
            'config/params.php',
        ],
        'yiisoft/assets' => [
            'config/params.php',
        ],
        'yiisoft/user' => [
            'config/params.php',
        ],
        'yiisoft/session' => [
            'config/params.php',
        ],
        'yiisoft/yii-web' => [
            'config/params.php',
        ],
        'yiisoft/aliases' => [
            'config/params.php',
        ],
        'yiisoft/router-fastroute' => [
            'config/params.php',
        ],
        'yiisoft/profiler' => [
            'config/params.php',
        ],
        'yiisoft/mailer-swiftmailer' => [
            'config/params.php',
        ],
        'yiisoft/view' => [
            'config/params.php',
        ],
        'yiisoft/log-target-file' => [
            'config/params.php',
        ],
        'yiisoft/cache-file' => [
            'config/params.php',
        ],
    ],
    'web' => [
        '/' => [
            '$common',
            'config/web/*.php',
        ],
        'yiisoft/yii-view' => [
            'config/web.php',
        ],
        'yiisoft/csrf' => [
            'config/web.php',
        ],
        'yiisoft/yii-debug-api' => [
            'config/web.php',
        ],
        'yiisoft/data-response' => [
            'config/web.php',
        ],
        'yiisoft/yii-debug' => [
            'config/web.php',
        ],
        'yiisoft/yii-event' => [
            'config/web.php',
        ],
        'yiisoft/yii-bootstrap5' => [
            'config/web/*.php',
        ],
        'yiisoft/assets' => [
            'config/web.php',
        ],
        'yiisoft/user' => [
            'config/web.php',
        ],
        'yiisoft/session' => [
            'config/web.php',
        ],
        'yiisoft/yii-web' => [
            'config/web.php',
        ],
        'yiisoft/middleware-dispatcher' => [
            'config/web.php',
        ],
        'yiisoft/router-fastroute' => [
            'config/web.php',
        ],
        'yiisoft/view' => [
            'config/web.php',
        ],
        'yiisoft/error-handler' => [
            'config/web.php',
        ],
    ],
    'providers' => [
        '/' => [
            'config/providers.php',
        ],
        'yiisoft/yii-debug' => [
            'config/providers.php',
        ],
        'yiisoft/yii-filesystem' => [
            'config/providers.php',
        ],
        'yiisoft/widget' => [
            'config/providers.php',
        ],
    ],
    'events-web' => [
        '/' => [
            '$events',
            'config/events-web.php',
        ],
        'yiisoft/yii-debug' => [
            'config/events-web.php',
        ],
        'yiisoft/yii-event' => [
            '$events',
            'config/events-web.php',
        ],
        'yiisoft/profiler' => [
            'config/events-web.php',
        ],
        'yiisoft/log' => [
            'config/events-web.php',
        ],
    ],
    'tests' => [
        'yiisoft/yii-debug' => [
            'config/tests.php',
        ],
        'yiisoft/yii-web' => [
            '$web',
        ],
    ],
    'console' => [
        '/' => [
            '$common',
            'config/console/*.php',
        ],
        'yiisoft/yii-debug' => [
            'config/console.php',
        ],
        'yiisoft/yii-cycle' => [
            'config/console.php',
        ],
        'yiisoft/yii-console' => [
            'config/console.php',
        ],
        'yiisoft/yii-event' => [
            'config/console.php',
        ],
    ],
    'events' => [
        '/' => [
            'config/events.php',
        ],
        'yiisoft/yii-event' => [
            'config/events.php',
        ],
    ],
    'events-console' => [
        '/' => [
            '$events',
            'config/events-console.php',
        ],
        'yiisoft/yii-debug' => [
            'config/events-console.php',
        ],
        'yiisoft/yii-cycle' => [
            'config/events-console.php',
        ],
        'yiisoft/yii-event' => [
            '$events',
            'config/events-console.php',
        ],
    ],
    'providers-console' => [
        '/' => [
            '$providers',
            'config/providers-console.php',
        ],
        'yiisoft/yii-console' => [
            'config/providers-console.php',
        ],
    ],
    'providers-web' => [
        '/' => [
            '$providers',
            'config/providers-web.php',
        ],
        'yiisoft/yii-debug-api' => [
            'config/providers-web.php',
        ],
        'yiisoft/yii-cycle' => [
            'config/providers-web.php',
        ],
    ],
    'routes' => [
        '/' => [
            'config/routes.php',
        ],
        'yiisoft/yii-debug-api' => [
            'config/routes.php',
        ],
    ],
];
