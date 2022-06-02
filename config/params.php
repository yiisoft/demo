<?php

declare(strict_types=1);

use App\Middleware\LocaleMiddleware;
use App\ViewInjection\CommonViewInjection;
use App\ViewInjection\LayoutViewInjection;
use App\ViewInjection\LinkTagsViewInjection;
use App\ViewInjection\MetaTagsViewInjection;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Cookies\CookieMiddleware;
use Yiisoft\Definitions\Reference;
use Yiisoft\ErrorHandler\Middleware\ErrorCatcher;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Router\Middleware\Router;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Session\SessionMiddleware;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\User\Login\Cookie\CookieLoginMiddleware;
use Yiisoft\Yii\Console\Application;
use Yiisoft\Yii\Console\Command\Serve;
use Yiisoft\Yii\Cycle\Schema\Conveyor\AttributedSchemaConveyor;
use Yiisoft\Yii\View\CsrfViewInjection;

return [
    'locales' => [
                  'ar'=> 'ar-AR',
                  'en' => 'en-US',
                  'ja' => 'ja-JP',
                  'nl' => 'nl-NL',
                  'ru' => 'ru-RU', 
                  'zh' => 'zh-CN'
    ],
    'mailer' => [
        'adminEmail' => 'admin@example.com',
        'senderEmail' => 'sender@example.com',
    ],
    'middlewares' => [
        ErrorCatcher::class,
        SessionMiddleware::class,
        CookieMiddleware::class,
        CookieLoginMiddleware::class,
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

    'yiisoft/form' => [
        'configs' => [
            'default' => [
                'containerClass' => 'form-floating mb-3',
                'inputClass' => 'form-control',
                'invalidClass' => 'is-invalid',
                'validClass' => 'is-valid',
                'template' => '{input}{label}{hint}{error}',
                'labelClass' => 'floatingInput',
                'errorClass' => 'fw-bold fst-italic',
                'hintClass' => 'form-text',
                'fieldConfigs' => [
                    \Yiisoft\Form\Field\SubmitButton::class => [
                        'buttonClass()' => ['btn btn-primary btn-lg mt-3'],
                        'containerClass()' => ['d-grid gap-2 form-floating'],
                    ],
                ],
            ],
        ],
    ],

    'yiisoft/rbac-rules-container' => [
        'rules' => require __DIR__ . '/rbac-rules.php',
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
            'currentRoute' => Reference::to(CurrentRoute::class),
            'translator' => Reference::to(TranslatorInterface::class),
        ],
    ],

    'yiisoft/cookies' => [
        'secretKey' => '53136271c432a1af377c3806c3112ddf',
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
        // Cycle DBAL config
        'dbal' => [
            /**
             * SQL query logger
             * You may use {@see \Yiisoft\Yii\Cycle\Logger\StdoutQueryLogger} class to pass log to
             * stdout or any PSR-compatible logger
             */
            //'query-logger' => \Psr\Log\LoggerInterface::class,
            'query-logger' => null,
            // Default database (from 'databases' list)
            'default' => 'default',
            'aliases' => [],
            'databases' => [
                'default' => ['connection' => 'mysql']
            ],
            'connections' => [
                'mysql'=> new \Cycle\Database\Config\MySQLDriverConfig(
                       connection: new \Cycle\Database\Config\MySQL\DsnConnectionConfig(
                               // dsn
                               'mysql:host=localhost;dbname=yii-invoice',
                               // user
                               'root',
                               // password
                               ''
                       ),
                       driver: \Cycle\Database\Driver\MySQL\MySQLDriver::class, 
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
        'entity-paths' => [
            '@src'
        ],
        'conveyor' => AttributedSchemaConveyor::class,
    ],
    'yiisoft/yii-swagger' => [
        'annotation-paths' => [
            '@src/Controller',
            '@src/User/Controller',
        ],
    ],
    
    // Performance setting
    'yiisoft/yii-debug' => ['enabled'=>true],         
    
    // Performance setting
    'yiisoft/yii-debug-api' => [
        'enabled' => true,
        'allowedIPs' => ['127.0.0.1', '::1'],
        'allowedHosts' => [],
    ], 
];
