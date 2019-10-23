<?php

use App\Command\User\CreateCommand;

return [
    'mailer.host' => 'smtp.example.com',
    'mailer.port' => 25,
    'mailer.encryption' => null,
    'mailer.username' => 'admin@example.com',
    'mailer.password' => '',

    'supportEmail' => 'support@example.com',

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
