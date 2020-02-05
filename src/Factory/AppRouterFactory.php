<?php

namespace App\Factory;

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
            Route::get('/', new ActionCaller(SiteController::class, 'index', $container))
                ->name('site/index'),
            Route::methods([Method::GET, Method::POST], '/contact', new ActionCaller(ContactController::class, 'contact', $container))
                ->name('site/contact'),
            Route::get('/test/{id:\w+}', new ActionCaller(SiteController::class, 'testParameter', $container))
                ->name('site/test'),
            Route::methods([Method::GET, Method::POST], '/login', new ActionCaller(AuthController::class, 'login', $container))
                ->name('site/login'),
            Route::get('/logout', new ActionCaller(AuthController::class, 'logout', $container))
                ->name('site/logout'),

            Route::get('/user[/page-{page:\d+}]', new ActionCaller(UserController::class, 'index', $container))
                 ->name('user/index'),
            Route::get('/user/{login}', new ActionCaller(UserController::class, 'profile', $container))
                 ->name('user/profile'),
        ];

        $router = (new RouterFactory(new FastRouteFactory(), $routes))($container);

        $router->addGroup('/blog', static function (RouteCollectorInterface $r) use (&$container) {
            $r->addRoute(
                Route::get('[/page{page:\d+}]', new ActionCaller(BlogController::class, 'index', $container))
                     ->name('blog/index')
            );
            $r->addRoute(
                Route::get('/archive/{year:\d+}-{month:\d+}[/page{page:\d+}]', new ActionCaller(BlogController::class, 'index', $container))
                     ->name('blog/archive')
            );
            $r->addRoute(
                Route::get('/page/{slug}', new ActionCaller(PostController::class, 'index', $container))
                     ->name('blog/page')
            );
            $r->addRoute(
                Route::get('/tag/{label}[/page{page:\d+}]', new ActionCaller(TagController::class, 'index', $container))
                     ->name('blog/tag')
            );
        });

        return $router;
    }
}
