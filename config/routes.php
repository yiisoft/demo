<?php

declare(strict_types=1);

use App\Blog\Archive\ArchiveController;
use App\Blog\BlogController;
use App\Blog\CommentController;
use App\Blog\Post\PostController;
use App\Blog\Tag\TagController;
use App\Contact\ContactController;
use App\Controller\ApiInfo;
use App\Controller\ApiUserController;
use App\Controller\AuthController;
use App\Controller\SignupController;
use App\Controller\SiteController;
use App\Controller\UserController;
use App\Middleware\AccessChecker;
use App\Middleware\ApiDataWrapper;
use Yiisoft\Auth\Middleware\Authentication;
use Yiisoft\Http\Method;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;
use Yiisoft\DataResponse\DataResponseFactoryInterface;
use Yiisoft\DataResponse\Middleware\FormatDataResponseAsJson;
use Yiisoft\DataResponse\Middleware\FormatDataResponseAsXml;
use Yiisoft\Swagger\Middleware\SwaggerJson;
use Yiisoft\Swagger\Middleware\SwaggerUi;

return [
    // Lonely pages of site
    Route::get('/', [SiteController::class, 'index'])
        ->name('site/index'),
    Route::methods([Method::GET, Method::POST], '/contact', [ContactController::class, 'contact'])
        ->name('site/contact'),
    Route::methods([Method::GET, Method::POST], '/login', [AuthController::class, 'login'])
        ->name('site/login'),
    Route::get('/logout', [AuthController::class, 'logout'])
        ->name('site/logout'),
    Route::methods([Method::GET, Method::POST], '/signup', [SignupController::class, 'signup'])
        ->name('site/signup'),

    // User
    Group::create('/user', [
        // Index
        Route::get('[/page-{page:\d+}]', [UserController::class, 'index'])
            ->name('user/index'),
        // Profile page
        Route::get('/{login}', [UserController::class, 'profile'])
            ->name('user/profile'),
    ]),

    // User
    Group::create('/api', [
        Route::get('/info/v1', function (DataResponseFactoryInterface $responseFactory) {
            return $responseFactory->createResponse(['version' => '1.0', 'author' => 'yiisoft']);
        })->name('api/info/v1'),
        Route::get('/info/v2', ApiInfo::class)
            ->addMiddleware(FormatDataResponseAsJson::class)
            ->name('api/info/v2'),
        Route::get('/user', [ApiUserController::class, 'index'])
            ->name('api/user/index'),
        Route::get('/user/{login}', [ApiUserController::class, 'profile'])
            ->addMiddleware(FormatDataResponseAsJson::class)
            ->name('api/user/profile'),
    ])->addMiddleware(ApiDataWrapper::class)->addMiddleware(FormatDataResponseAsXml::class),

    // Blog routes
    Group::create('/blog', [
        // Index
        Route::get('[/page{page:\d+}]', [BlogController::class, 'index'])
            ->name('blog/index'),
        // Add Post page
        Route::methods([Method::GET, Method::POST], '/page/add', [PostController::class, 'add'])
            ->name('blog/add')->addMiddleware(Authentication::class),
        // Edit Post page
        Route::methods([Method::GET, Method::POST], '/page/edit/{slug}', [PostController::class, 'edit'])
            ->name('blog/edit')
            ->addMiddleware(Authentication::class)
            ->addMiddleware(fn (AccessChecker $checker) => $checker->withPermission('editPost')),
        // Post page
        Route::get('/page/{slug}', [PostController::class, 'index'])
            ->name('blog/post'),
        // Tag page
        Route::get('/tag/{label}[/page{page:\d+}]', [TagController::class, 'index'])
            ->name('blog/tag'),
        // Archive
        Group::create('/archive', [
            // Index page
            Route::get('', [ArchiveController::class, 'index'])
                ->name('blog/archive/index'),
            // Yearly page
            Route::get('/{year:\d+}', [ArchiveController::class, 'yearlyArchive'])
                ->name('blog/archive/year'),
            // Monthly page
            Route::get('/{year:\d+}-{month:\d+}[/page{page:\d+}]', [ArchiveController::class, 'monthlyArchive'])
                ->name('blog/archive/month')
        ]),
        // comments
        Route::get('/comments/[next/{next}]', [CommentController::class, 'index'])
            ->name('blog/comment/index'),
    ]),

    // Swagger routes
    Group::create('/swagger', [
        Route::get('')
            ->addMiddleware(fn (SwaggerUi $swaggerUi) => $swaggerUi->withJsonUrl('/swagger/json-url'))
            ->name('swagger/index'),
        Route::get('/json-url')
            ->addMiddleware(static function (SwaggerJson $swaggerJson) {
                return $swaggerJson
                    // Uncomment cache for production environment
                    // ->withCache(60)
                    ->withAnnotationPaths([
                        '@src/Controller' // Path to API controllers
                    ]);
            })
            ->addMiddleware(FormatDataResponseAsJson::class),
    ]),
];
