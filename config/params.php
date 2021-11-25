<?php

declare(strict_types=1);

use App\Middleware\LocaleMiddleware;
use App\ViewInjection\CommonViewInjection;
use App\ViewInjection\LayoutViewInjection;
use App\ViewInjection\LinkTagsViewInjection;
use App\ViewInjection\MetaTagsViewInjection;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Definitions\Reference;
use Yiisoft\ErrorHandler\Middleware\ErrorCatcher;
use Yiisoft\Router\CurrentRouteInterface;
use Yiisoft\Router\Middleware\Router;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Session\SessionMiddleware;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Yii\Console\Application;
use Yiisoft\Yii\Console\Command\Serve;
use Yiisoft\Yii\Cycle\Schema\Conveyor\AttributedSchemaConveyor;
use Yiisoft\Yii\View\CsrfViewInjection;

return [
    'locales' => ['en' => 'en-US', 'ru' => 'ru-RU'],
    'mailer' => [
        'adminEmail' => 'admin@example.com',
        'senderEmail' => 'sender@example.com',
    ],
    'middlewares' => [
        ErrorCatcher::class,
        SessionMiddleware::class,
        LocaleMiddleware::class,
        Router::class,
    ],

    'yiisoft/aliases' => [
        'aliases' => [
            '@root' => dirname(__DIR__),
            '@assets' => '@root/public/assets',
            '@assetsUrl' => '@baseUrl/assets',
            '@baseUrl' => '/',
            '@messages' => '@resources/messages',
            '@npm' => '@root/node_modules',
            '@public' => '@root/public',
            '@resources' => '@root/resources',
            '@runtime' => '@root/runtime',
            '@src' => '@root/src',
            '@vendor' => '@root/vendor',
            '@layout' => '@root/views/layout',
            '@views' => '@root/views',
        ],
    ],

    'yiisoft/router-fastroute' => [
        'enableCache' => false,
    ],

    'yiisoft/translator' => [
        'locale' => 'en',
        'fallbackLocale' => 'en',
        'defaultCategory' => 'app',
        'categorySources' => [
            // You can add categories from your application and additional modules using `Reference::to` below
            // Reference::to(ApplicationCategorySource::class),
            Reference::to('translation.app'),
        ],
    ],

    'yiisoft/view' => [
        'basePath' => '@views',
        'parameters' => [
            'assetManager' => Reference::to(AssetManager::class),
            'urlGenerator' => Reference::to(UrlGeneratorInterface::class),
            'currentRoute' => Reference::to(CurrentRouteInterface::class),
            'translator' => Reference::to(TranslatorInterface::class),
        ],
    ],

    'yiisoft/yii-view' => [
        'viewPath' => '@views',
        'layout' => '@views/layout/main',
        'injections' => [
            Reference::to(CommonViewInjection::class),
            Reference::to(CsrfViewInjection::class),
            Reference::to(LayoutViewInjection::class),
            Reference::to(LinkTagsViewInjection::class),
            Reference::to(MetaTagsViewInjection::class),
        ],
    ],

    'yiisoft/yii-console' => [
        'name' => Application::NAME,
        'version' => Application::VERSION,
        'autoExit' => false,
        'commands' => [
            'serve' => Serve::class,
            'user/create' => App\User\Console\CreateCommand::class,
            'user/assignRole' => App\User\Console\AssignRoleCommand::class,
            'fixture/add' => App\Command\Fixture\AddCommand::class,
            'router/list' => App\Command\Router\ListCommand::class,
            'translator/translate' => App\Command\Translation\TranslateCommand::class,
        ],
    ],

    'yiisoft/yii-cycle' => [
        // DBAL config
        'dbal' => [
            // SQL query logger. Definition of Psr\Log\LoggerInterface
            // For example, \Yiisoft\Yii\Cycle\Logger\StdoutQueryLogger::class
            'query-logger' => \Psr\Log\LoggerInterface::class,
            // Default database
            'default' => 'default',
            'aliases' => [],
            'databases' => [
                'default' => ['connection' => 'sqlite'],
            ],
            'connections' => [
                'sqlite' => new \Cycle\Database\Config\SQLiteDriverConfig(
                    connection: new \Cycle\Database\Config\SQLite\FileConnectionConfig(
                        database: 'runtime/database1.db'
                    )
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
         * ]
         */
        'schema-providers' => [
            // Uncomment next line to enable schema cache
            // \Yiisoft\Yii\Cycle\Schema\Provider\SimpleCacheSchemaProvider::class => ['key' => 'cycle-orm-cache-key'],
            \Yiisoft\Yii\Cycle\Schema\Provider\FromConveyorSchemaProvider::class => [
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
];
