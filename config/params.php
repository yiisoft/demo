<?php

use App\Command\User\CreateCommand;

return [
    'mailer' => [
        'host' => 'smtp.example.com',
        'port' => 25,
        'encryption' => null,
        'username' => 'admin@example.com',
        'password' => '',
    ],

    'supportEmail' => 'support@example.com',

    'aliases' => [
        '@root' => dirname(__DIR__),
        '@views' => '@root/views',
        '@resources' => '@root/resources',
        '@src' => '@root/src',
    ],

    'session' => [
        'options' => ['cookie_secure' => 0],
    ],
    
    'commands' => [
        'user/create' => CreateCommand::class,
    ],

    // cycle DBAL config
    'cycle.dbal' => [
        'default' => 'default',
        'aliases' => [],
        'databases' => [
            'default' => ['connection' => 'sqlite']
        ],
        'connections' => [
            'sqlite' => [
                'driver' => \Spiral\Database\Driver\SQLite\SQLiteDriver::class,
                'connection' => 'sqlite:@runtime/database.db',
                'username' => '',
                'password' => '',
            ]
        ],
    ],
    // cycle common config
    'cycle.common' => [
        'entityPaths' => [
            '@src/Entity'
        ],
    ],
    // cycle migration config
    'cycle.migrations' => [
        'directory' => '@root/migrations',
        'namespace' => 'App\\Migration',
        'table' => 'migration',
        'safe' => false,
    ],
];
