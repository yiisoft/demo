<?php

declare(strict_types=1);

use App\Auth\Controller\AuthController;
use App\Auth\Controller\SignupController;
use App\Auth\Controller\ChangePasswordController;
use App\Blog\Archive\ArchiveController;
use App\Blog\BlogController;
use App\Blog\CommentController;
use App\Blog\Post\PostController;
use App\Blog\Post\PostRepository;
use App\Blog\Tag\TagController;
use App\Contact\ContactController;
use App\Controller\Actions\ApiInfo;
use App\Controller\SiteController;
use App\Middleware\AccessChecker;
use App\Middleware\ApiDataWrapper;
use App\User\Controller\ApiUserController;
use App\User\Controller\UserController;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Auth\Middleware\Authentication;
use Yiisoft\DataResponse\DataResponseFactoryInterface;
use Yiisoft\DataResponse\Middleware\FormatDataResponseAsHtml;
use Yiisoft\DataResponse\Middleware\FormatDataResponseAsJson;
use Yiisoft\DataResponse\Middleware\FormatDataResponseAsXml;
use Yiisoft\Http\Method;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Swagger\Middleware\SwaggerJson;
use Yiisoft\Swagger\Middleware\SwaggerUi;
use Yiisoft\Yii\Middleware\CorsAllowAll;
use Yiisoft\Yii\Middleware\HttpCache;
use Yiisoft\Yii\RateLimiter\Counter;
use Yiisoft\Yii\RateLimiter\LimitRequestsMiddleware;
use Yiisoft\Yii\RateLimiter\Storage\StorageInterface;

return [
    // Lonely pages of site
    Route::get('/')
        ->action([SiteController::class, 'index'])
        ->name('site/index'),
    Route::methods([Method::GET, Method::POST], '/contact')
        ->action([ContactController::class, 'contact'])
        ->name('site/contact'),

    // Auth
    Route::methods([Method::GET, Method::POST], '/login')
        ->middleware(LimitRequestsMiddleware::class)
        ->action([AuthController::class, 'login'])
        ->name('auth/login'),
    Route::post('/logout')
        ->action([AuthController::class, 'logout'])
        ->name('auth/logout'),
    Route::methods([Method::GET, Method::POST], '/signup')
        ->middleware(fn (
            ResponseFactoryInterface $responseFactory,
            StorageInterface $storage
        ) => new LimitRequestsMiddleware(new Counter($storage, 10, 10), $responseFactory))
        ->action([SignupController::class, 'signup'])
        ->name('auth/signup'),
    Route::methods([Method::GET, Method::POST], '/change')
        ->middleware(fn(
            ResponseFactoryInterface $responseFactory,
            StorageInterface $storage
        ) => new LimitRequestsMiddleware(new Counter($storage, 10, 10), $responseFactory))
        ->action([ChangePasswordController::class, 'change'])
        ->name('auth/change'),

    Group::create('/user')
        ->routes(
            // User
            Route::methods(['GET', 'POST'], '[/{page:\d+}/{pagesize:\d+}]')
                ->name('user/index')
                ->action([UserController::class, 'index']),
            // Profile page
            Route::get('/{login}')
                ->action([UserController::class, 'profile'])
                ->name('user/profile'),
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
            Route::get('[/page{page:\d+}]')
                ->middleware(
                    fn (HttpCache $httpCache, PostRepository $postRepository) => $httpCache->withLastModified(function (ServerRequestInterface $request, $params) use ($postRepository) {
                        return $postRepository
                            ->getMaxUpdatedAt()
                            ->getTimestamp();
                    })
                )
                ->action([BlogController::class, 'index'])
                ->name('blog/index'),
            // Add Post page
            Route::methods([Method::GET, Method::POST], '/page/add')
                ->middleware(Authentication::class)
                ->action([PostController::class, 'add'])
                ->name('blog/add'),
            // Edit Post page
            Route::methods([Method::GET, Method::POST], '/page/edit/{slug}')
                ->name('blog/edit')
                ->middleware(Authentication::class)
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editPost'))
                ->action([PostController::class, 'edit']),

            // Post page
            Route::get('/page/{slug}')
                ->middleware(
                    fn (HttpCache $httpCache, PostRepository $postRepository, CurrentRoute $currentRoute) => $httpCache->withEtagSeed(function (ServerRequestInterface $request, $params) use ($postRepository, $currentRoute) {
                        $post = $postRepository->findBySlug($currentRoute->getArgument('slug'));

                        return $post->getSlug() . '-' . $post
                                ->getUpdatedAt()
                                ->getTimestamp();
                    })
                )
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
            Route::methods(['GET', 'POST'], '/comments[/{page:\d+}/{pagesize:\d+}]')
                ->action([CommentController::class, 'index'])
                ->name('blog/comment/index')
        ),

    // Swagger routes
    Group::create('/docs')
        ->routes(
            Route::get('')
                ->middleware(FormatDataResponseAsHtml::class)
                ->action(function (SwaggerUi $swaggerUi, UrlGeneratorInterface $urlGenerator) {
                    return $swaggerUi->withJsonUrl($urlGenerator->getUriPrefix() . '/docs/openapi.json');
                })
                ->name('swagger/index'),
            Route::get('/openapi.json')
                ->middleware(FormatDataResponseAsJson::class)
                ->middleware(CorsAllowAll::class)
                ->action([SwaggerJson::class, 'process']),
        ),
];
