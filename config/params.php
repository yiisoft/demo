<?php

use App\Command;
use Cycle\Schema\Generator;

return [
    'yiisoft/yii-debug' => [
        // 'enabled' => false,
    ],
    'mailer' => [
        'writeToFiles' => true,
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

    'yiisoft/yii-console' => [
        'commands' => [
            'user/create' => Command\User\CreateCommand::class,
            'fixture/add' => Command\Fixture\AddCommand::class,
            'router/list' => Command\Router\ListCommand::class,
        ],
    ],

    'yiisoft/yii-cycle' => [
        'dbal' => [
            'default'     => 'default',
            'aliases'     => [],
            'databases'   => [
                'default' => ['connection' => 'sqlite']
            ],
            'connections' => [
                'sqlite' => [
                    'driver'     => \Spiral\Database\Driver\SQLite\SQLiteDriver::class,
                    'connection' => 'sqlite:@runtime/database.db',
                    'username'   => '',
                    'password'   => '',
                ],
            ],
            // 'query-logger' => \Yiisoft\Yii\Cycle\Logger\StdoutQueryLogger::class,
        ],
        // 'orm-promise-factory' => \Cycle\ORM\Promise\ProxyFactory::class,
        'migrations' => [
            'directory' => '@root/migrations',
            'namespace' => 'App\\Migration',
            'table' => 'migration',
            'safe' => false,
        ],
        'schema-providers' => [
            \Yiisoft\Yii\Cycle\Schema\Provider\SimpleCacheSchemaProvider::class => [
                'key' => 'cycle-orm-cache-key'
            ],
            // \Yiisoft\Yii\Cycle\Schema\Provider\FromFileSchemaProvider::class => [
            //     'file' => '@runtime/schema.php'
            // ],
            \Yiisoft\Yii\Cycle\Schema\Provider\FromConveyorSchemaProvider::class => [
                'generators' => [
                    Generator\SyncTables::class, // sync table changes to database
                ]
            ],
        ],
        'annotated-entity-paths' => [
            '@src/Entity',
            '@src/Blog/Entity',
        ],
    ],
];
