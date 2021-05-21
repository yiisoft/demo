<?php

declare(strict_types=1);

use Cycle\ORM\PromiseFactoryInterface;
use Yiisoft\Yii\Cycle\Command\Schema;
use Yiisoft\Yii\Cycle\Command\Migration;
use Yiisoft\Yii\Cycle\Schema\SchemaProviderInterface;

return [
    // Console commands
    'yiisoft/yii-console' => [
        'commands' => [
            'cycle/schema' => Schema\SchemaCommand::class,
            'cycle/schema/php' => Schema\SchemaPhpCommand::class,
            'cycle/schema/clear' => Schema\SchemaClearCommand::class,
            'cycle/schema/rebuild' => Schema\SchemaRebuildCommand::class,
            'migrate/create' => Migration\CreateCommand::class,
            'migrate/generate' => Migration\GenerateCommand::class,
            'migrate/up' => Migration\UpCommand::class,
            'migrate/down' => Migration\DownCommand::class,
            'migrate/list' => Migration\ListCommand::class,
        ],
    ],

     'yiisoft/yii-cycle' => [
        // Cycle DBAL config
        'dbal' => [
            /**
             * SQL query logger
             * You may use {@see \Yiisoft\Yii\Cycle\Logger\StdoutQueryLogger} class to pass log to
             * stdout or any PSR-compatible logger
             */
            'query-logger' => null,
            // Default database (from 'databases' list)
            'default' => 'default',
            'aliases' => [],
            'databases' => [
                'default' => ['connection' => 'mysql']
            ],
            'connections' => [
                'mysql' => [
                    'driver' => \Spiral\Database\Driver\MySQL\MySQLDriver::class,
                    // see https://www.php.net/manual/pdo.construct.php, DSN for connection syntax
                    'connection' => 'mysql:host=localhost;dbname=yii-invoice',
                    'username' => 'root',
                    'password' => '',
                ]
            ],
        ],

        // Migrations config
        'migrations' => [
            'directory' => '@root/migrations',
            'namespace' => 'App\\Migration',
            'table' => 'migration',
            'safe' => false,
        ],

        /**
         * Config for {@see \Yiisoft\Yii\Cycle\Factory\OrmFactory}
         * Null, classname or {@see PromiseFactoryInterface} object.
         *
         * For example, \Cycle\ORM\Promise\ProxyFactory::class
         *
         * @link https://github.com/cycle/docs/blob/master/advanced/promise.md
         */
        'orm-promise-factory' => null,
        //'orm-promise-factory' => \Cycle\ORM\Promise\ProxyFactory::class, 
        /**
         * A list of DB schema providers for {@see \Yiisoft\Yii\Cycle\Schema\Provider\Support\SchemaProviderPipeline}
         * Providers are implementing {@see SchemaProviderInterface}.
         * The configuration is an array of provider class names. Alternatively, you can specify provider class as key
         * and its config as value:
         */
        'schema-providers' => [
            // Uncomment next line to enable schema cache
            //\Yiisoft\Yii\Cycle\Schema\Provider\SimpleCacheSchemaProvider::class => ['key' => 'cycle-orm-cache-key'],
            //\Yiisoft\Yii\Cycle\Schema\Provider\FromFilesSchemaProvider::class => [
            //   'files' => ['@runtime/cycle-schema.php']
            //],
            \Yiisoft\Yii\Cycle\Schema\Provider\FromConveyorSchemaProvider::class => [
                'generators' => [
                    Cycle\Schema\Generator\SyncTables::class, // sync table changes to database
                ],
           ],
        ],

        /**
         * {@see \Yiisoft\Yii\Cycle\Schema\Conveyor\AnnotatedSchemaConveyor} settings
         * A list of entity directories. You can use {@see \Yiisoft\Aliases\Aliases} in paths.
         */
        'annotated-entity-paths' => [
            '@src'
        ],
    ],
];