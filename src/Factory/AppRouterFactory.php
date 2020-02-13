<?php

namespace App\Factory;

use App\Blog\Archive\ArchiveController;
use App\Blog\BlogController;
use App\Blog\Post\PostController;
use App\Blog\Tag\TagController;
use App\Controller\AuthController;
use App\Controller\ContactController;
use App\Controller\SiteController;
use App\Controller\UserController;
use Psr\Container\ContainerInterface;
use Yiisoft\Http\Method;
use Yiisoft\Router\FastRoute\FastRouteFactory;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;
use Yiisoft\Router\RouteCollectorInterface;
use Yiisoft\Router\RouterFactory;
use Yiisoft\Yii\Web\Middleware\ActionCaller;

class AppRouterFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $routes = [
            Route::get('/stream', new ActionCaller(SiteController::class, 'stream', $container))
                ->name('stream'),
            Route::get('/', new ActionCaller(SiteController::class, 'index', $container))
                ->name('site/index'),
            Route::methods(
                [Method::GET, Method::POST],
                '/contact',
                new ActionCaller(ContactController::class, 'contact', $container)
            )->name('site/contact'),
            Route::get('/test/{id:\w+}', new ActionCaller(SiteController::class, 'testParameter', $container))
                ->name('site/test'),
            Route::methods(
                [Method::GET, Method::POST],
                '/login',
                new ActionCaller(AuthController::class, 'login', $container)
            )->name('site/login'),
            Route::get('/logout', new ActionCaller(AuthController::class, 'logout', $container))
                ->name('site/logout'),

            Route::get('/user[/page-{page:\d+}]', new ActionCaller(UserController::class, 'index', $container))
                 ->name('user/index'),
            Route::get('/user/{login}', new ActionCaller(UserController::class, 'profile', $container))
                 ->name('user/profile'),
        ];

        $router = (new RouterFactory(new FastRouteFactory(), $routes))($container);

        // Blog routes
        $router->addGroup(new Group('/blog', static function (RouteCollectorInterface $r) use ($container) {
            // Index
            $r->addRoute(
                Route::get('[/page{page:\d+}]', new ActionCaller(BlogController::class, 'index', $container))
                     ->name('blog/index')
            );
            // Archive
            $r->addGroup(new Group('/archive', function (RouteCollectorInterface $r) use ($container) {
                $r->addRoute(
                    Route::get(
                        '',
                        new ActionCaller(ArchiveController::class, 'index', $container)
                    )->name('blog/archive/index')
                );
                $r->addRoute(
                    Route::get(
                        '/{year:\d+}',
                        new ActionCaller(ArchiveController::class, 'yearlyArchive', $container)
                    )->name('blog/archive/year')
                );
                $r->addRoute(
                    Route::get(
                        '/{year:\d+}-{month:\d+}[/page{page:\d+}]',
                        new ActionCaller(ArchiveController::class, 'monthlyArchive', $container)
                    )->name('blog/archive/month')
                );
            }));
            // Page
            $r->addRoute(
                Route::get('/page/{slug}', new ActionCaller(PostController::class, 'index', $container))
                     ->name('blog/post')
            );
            // Tag
            $r->addRoute(
                Route::get('/tag/{label}[/page{page:\d+}]', new ActionCaller(TagController::class, 'index', $container))
                     ->name('blog/tag')
            );
        }));

        return $router;
    }
}
