<?php

declare(strict_types=1);

use App\Queue\LoggingAuthorizationHandler;
use Cycle\Database\Config\SQLite\FileConnectionConfig;
use Cycle\Database\Config\SQLiteDriverConfig;
use Yiisoft\ErrorHandler\Middleware\ErrorCatcher;
use Yiisoft\Router\Middleware\Router;
use Yiisoft\Yii\Cycle\Schema\Conveyor\AttributedSchemaConveyor;
use Yiisoft\Yii\Cycle\Schema\Provider\FromConveyorSchemaProvider;
use Yiisoft\Yii\Cycle\Schema\Provider\PhpFileSchemaProvider;
use Yiisoft\Yii\Cycle\Schema\SchemaProviderInterface;
use Yiisoft\Yii\Middleware\Locale;
use Yiisoft\Yii\Middleware\Subfolder;
use Yiisoft\Yii\Queue\Adapter\SynchronousAdapter;

return [
    'locale' => [
        'locales' => ['en' => 'en-US', 'ru' => 'ru-RU', 'de' => 'de-DE'],
        'ignoredRequests' => [
            '/debug**',
            '/inspect**',
        ],
    ],
    'supportEmail' => 'support@example.com',
    'middlewares' => [
        ErrorCatcher::class,
        Subfolder::class,
        Locale::class,
        Router::class,
    ],

    'yiisoft/aliases' => [
        'aliases' => [
            '@root' => dirname(__DIR__, 2),
            '@assets' => '@public/assets',
            '@assetsUrl' => '@baseUrl/assets',
            '@baseUrl' => '',
            '@data' => '@root/data',
            '@messages' => '@resources/messages',
            '@public' => '@root/public',
            '@resources' => '@root/resources',
            '@runtime' => '@root/runtime',
            '@src' => '@root/src',
            '@tests' => '@root/tests',
            '@views' => '@root/views',
            '@vendor' => '@root/vendor',
        ],
    ],

    'yiisoft/router-fastroute' => [
        'enableCache' => false,
    ],

    'yiisoft/translator' => [
        'locale' => 'en',
        'fallbackLocale' => 'en',
        'defaultCategory' => 'app',
    ],

    'yiisoft/yii-cycle' => [
        // DBAL config
        'dbal' => [
            // SQL query logger. Definition of Psr\Log\LoggerInterface
            // For example, \Yiisoft\Yii\Cycle\Logger\StdoutQueryLogger::class
            'query-logger' => null,
            // Default database
            'default' => 'default',
            'aliases' => [],
            'databases' => [
                'default' => ['connection' => 'sqlite'],
            ],
            'connections' => [
                'sqlite' => new SQLiteDriverConfig(
                    new FileConnectionConfig(dirname(__DIR__, 2) . '/runtime/database.db')
                ),
            ],
        ],

        // Cycle migration config
        'migrations' => [
            'directory' => '@root/migrations',
            'namespace' => 'App\\Migration',
            'table' => 'migration',
            'safe' => false,
        ],

        /**
         * SchemaProvider list for {@see \Yiisoft\Yii\Cycle\Schema\Provider\Support\SchemaProviderPipeline}
         * Array of classname and {@see SchemaProviderInterface} object.
         * You can configure providers if you pass classname as key and parameters as array:
         * [
         *     SimpleCacheSchemaProvider::class => [
         *         'key' => 'my-custom-cache-key'
         *     ],
         *     FromFilesSchemaProvider::class => [
         *         'files' => ['@runtime/cycle-schema.php']
         *     ],
         *     FromConveyorSchemaProvider::class => [
         *         'generators' => [
         *              Generator\SyncTables::class, // sync table changes to database
         *          ]
         *     ],
         * ].
         */
        'schema-providers' => [
            // Uncomment next line to enable a Schema caching in the common cache
            // \Yiisoft\Yii\Cycle\Schema\Provider\SimpleCacheSchemaProvider::class => ['key' => 'cycle-orm-cache-key'],

            // Store generated Schema in the file
            PhpFileSchemaProvider::class => [
                'mode' => PhpFileSchemaProvider::MODE_WRITE_ONLY,
                'file' => '@runtime/schema.php',
            ],

            FromConveyorSchemaProvider::class => [
                'generators' => [
                    Cycle\Schema\Generator\SyncTables::class, // sync table changes to database
                ],
            ],
        ],

        /**
         * Config for {@see \Yiisoft\Yii\Cycle\Schema\Conveyor\AnnotatedSchemaConveyor}
         * Annotated entity directories list.
         * {@see \Yiisoft\Aliases\Aliases} are also supported.
         */
        'entity-paths' => [
            '@src',
        ],
        'conveyor' => AttributedSchemaConveyor::class,
    ],
    'yiisoft/yii-swagger' => [
        'annotation-paths' => [
            '@src',
        ],
    ],

    'yiisoft/yii-queue' => [
        'handlers' => [
            LoggingAuthorizationHandler::NAME => [LoggingAuthorizationHandler::class, 'handle'],
        ],
        'channel-definitions' => [
            LoggingAuthorizationHandler::CHANNEL => SynchronousAdapter::class,
        ],
    ],

    'yiisoft/yii-debug-api' => [
        'allowedIPs' => ['172.0.0.1/10'],
    ],
];
