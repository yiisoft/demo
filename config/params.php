<?php

use App\Command;
use Cycle\Schema\Generator;

return [
    'debugger.enabled' => true,
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

    'console' => [
        'commands' => [
            'user/create' => Command\User\CreateCommand::class,
            'fixture/add' => Command\Fixture\AddCommand::class,
        ],
    ],

    // cycle DBAL config
    'cycle.dbal' => [
        'default' => 'default',
        'aliases' => [],
        'databases' => [
            'default' => ['connection' => 'sqldb'],
        ],
        'connections' => [
            'sqldb' => [
                'driver' => $_ENV['DBAL_DB_DRIVER'] ?? \Spiral\Database\Driver\SQLite\SQLiteDriver::class,
                'connection' => $_ENV['DBAL_DB_CONNECTION'] ?? 'sqlite:@runtime/database.db',
                'username' => $_ENV['DBAL_DB_USERNAME'] ?? '',
                'password' => $_ENV['DBAL_DB_PASSWORD'] ?? '',
            ],
        ],
    ],

    // cycle common config
    'cycle.common' => [
        'entityPaths' => [
            '@src/Entity',
            '@src/Blog/Entity',
        ],
        'cacheEnabled' => true,
        'cacheKey' => 'Cycle-ORM-Schema',
        'generators' => [
            // sync table changes to database
            Generator\SyncTables::class,
        ],
        // 'promiseFactory' => \Cycle\ORM\Promise\ProxyFactory::class,
        //'queryLogger' => \Yiisoft\Yii\Cycle\Logger\StdoutQueryLogger::class,
    ],
    // cycle migration config
    'cycle.migrations' => [
        'directory' => '@root/migrations',
        'namespace' => 'App\\Migration',
        'table' => 'migration',
        'safe' => false,
    ],
];
