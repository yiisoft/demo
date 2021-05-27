<?php

declare(strict_types=1);

use App\Blog\Archive\ArchiveController;
use App\Blog\BlogController;
use App\Blog\CommentController;
use App\Blog\Post\PostController;
use App\Blog\Tag\TagController;
use App\Contact\ContactController;
use App\Invoice\InvoiceController;
use App\Invoice\Client\ClientController;
use App\Controller\ApiInfo;
use App\Controller\AuthController;
use App\Controller\SignupController;
use App\Controller\SiteController;
use App\User\Controller\ApiUserController;
use App\Middleware\AccessChecker;
use App\Middleware\ApiDataWrapper;
use App\User\Controller\UserController;
use Yiisoft\Auth\Middleware\Authentication;
use Yiisoft\DataResponse\DataResponseFactoryInterface;
use Yiisoft\DataResponse\Middleware\FormatDataResponseAsHtml;
use Yiisoft\DataResponse\Middleware\FormatDataResponseAsJson;
use Yiisoft\DataResponse\Middleware\FormatDataResponseAsXml;
use Yiisoft\Http\Method;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;
use Yiisoft\Swagger\Middleware\SwaggerJson;
use Yiisoft\Swagger\Middleware\SwaggerUi;

return [
    // Lonely pages of site
    Route::get('/')
        ->action([SiteController::class, 'index'])
        ->name('site/index'),
    Route::methods([Method::GET, Method::POST], '/contact')
        ->action([ContactController::class, 'contact'])
        ->name('site/contact'),
    Route::methods([Method::GET, Method::POST], '/login')
        ->action([AuthController::class, 'login'])
        ->name('site/login'),
    Route::post('/logout')
        ->action([AuthController::class, 'logout'])
        ->name('site/logout'),
    Route::methods([Method::GET, Method::POST], '/signup')
        ->action([SignupController::class, 'signup'])
        ->name('site/signup'),

    // User
    Group::create('/user')
        ->routes(
        // Index
            Route::get('[/page-{page:\d+}]')
                ->action([UserController::class, 'index'])
                ->name('user/index'),
            // Profile page
            Route::get('/{login}')
                ->action([UserController::class, 'profile'])
                ->name('user/profile')
        ),

    // API group.
    // By default it responds with XML regardless of content-type.
    // Individual sub-routes are responding with JSON.
    Group::create('/api')
        ->middleware(FormatDataResponseAsXml::class)
        ->middleware(ApiDataWrapper::class)
        ->routes(
            Route::get('/info/v1')
                ->name('api/info/v1')
                ->action(function (DataResponseFactoryInterface $responseFactory) {
                    return $responseFactory->createResponse(['version' => '1.0', 'author' => 'yiisoft']);
                }),
            Route::get('/info/v2')
                ->name('api/info/v2')
                ->middleware(FormatDataResponseAsJson::class)
                ->action(ApiInfo::class),
            Route::get('/user')
                ->name('api/user/index')
                ->action([ApiUserController::class, 'index']),
            Route::get('/user/{login}')
                ->name('api/user/profile')
                ->middleware(FormatDataResponseAsJson::class)
                ->action([ApiUserController::class, 'profile'])
        ),

    // Blog routes
    Group::create('/blog')
        ->routes(
        // Index
            Route::get('[/page///////{page:\d+}]')
                ->action([BlogController::class, 'index'])
                ->name('blog/index'),
            // Add Post page
            Route::methods([Method::GET, Method::POST], '/this is the deceptive path and you can add an input in curly brackets for edits')
                ->middleware(Authentication::class)
                //this is the actual public function add(... in the controller. Note no keyword action before the name as in Yii2
                ->action([PostController::class, 'add'])
                //below is the name that will be used in PostController parameters 
                ->name('post/add'),
            // Edit Post page
            Route::methods([Method::GET, Method::POST], '/anything can be put here, so use .../config/routes.php/{slug}')
                ->name('post/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editPost'))
                ->middleware(Authentication::class)
                ->action([PostController::class, 'edit']),

            // Post page
            Route::get('/page/{slug}')
                ->action([PostController::class, 'index'])
                ->name('blog/post'),
            // Tag page
            Route::get('/tag/{label}[/page{page:\d+}]')
                ->action([TagController::class, 'index'])
                ->name('blog/tag'),
            // Archive
            Group::create('/archive')
                ->routes(
                // Index page
                    Route::get('')
                        ->action([ArchiveController::class, 'index'])
                        ->name('blog/archive/index'),
                    // Yearly page
                    Route::get('/{year:\d+}')
                        ->action([ArchiveController::class, 'yearlyArchive'])
                        ->name('blog/archive/year'),
                    // Monthly page
                    Route::get('/{year:\d+}-{month:\d+}[/page{page:\d+}]')
                        ->action([ArchiveController::class, 'monthlyArchive'])
                        ->name('blog/archive/month'),
                ),
            // comments
            Route::get('/comments/[next/{next}]')
                ->action([CommentController::class, 'index'])
                ->name('blog/comment/index')
        ),                      

    // Swagger routes
    Group::create('/swagger')
        ->routes(
            Route::get('')
                ->middleware(FormatDataResponseAsHtml::class)
                ->action(fn (SwaggerUi $swaggerUi) => $swaggerUi->withJsonUrl('/swagger/json-url'))
                ->name('swagger/index'),
            Route::get('/json-url')
                ->middleware(FormatDataResponseAsJson::class)
                ->action(static function (SwaggerJson $swaggerJson) {
                    return $swaggerJson
                        // Uncomment cache for production environment
                        // ->withCache(60)
                        ->withAnnotationPaths([
                            '@src/Controller', // Path to API controllers
                        ]);
                }),
        ),
    
    // Invoice routes
    Group::create('/invoice')
        ->routes(
        // Index
            //so nothing will appear over the tooltip when you hover over the button ie. ''                
            Route::get('')
                ->action([InvoiceController::class, 'index'])
                ->name('invoice/index'),
            //so if you add the below /client to the above '/invoice' you get '/invoice/client', does that look familiar?
            Route::get('/client')
                ->action([ClientController::class, 'index'])
                ->name('client/index'),    
            // Add Client
            Route::methods([Method::GET, Method::POST], '/client/add')
                ->middleware(Authentication::class)
                ->action([ClientController::class, 'add'])
                ->name('client/add'),
            // Edit Client
            Route::methods([Method::GET, Method::POST], '/client/edit/{client_id}')
                ->name('client/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editClient'))
                ->middleware(Authentication::class)
                ->action([ClientController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/client/delete/{client_id}')
                ->name('client/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editClient'))
                ->middleware(Authentication::class)
                ->action([ClientController::class, 'delete']),    
        ),                    
];
