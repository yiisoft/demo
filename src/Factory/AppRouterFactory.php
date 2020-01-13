<?php
namespace App\Factory;

use App\Controller\AuthController;
use App\Controller\ContactController;
use App\Controller\BlogController;
use App\Controller\SiteController;
use App\Controller\UserController;
use Psr\Container\ContainerInterface;
use Yiisoft\Router\FastRoute\FastRouteFactory;
use Yiisoft\Router\Group;
use Yiisoft\Router\Method;
use Yiisoft\Router\Route;
use Yiisoft\Router\RouteCollectorInterface;
use Yiisoft\Router\RouterFactory;
use Yiisoft\Yii\Web\Middleware\ActionCaller;

class AppRouterFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $routes = [
            Route::get('/')
                ->to(new ActionCaller(SiteController::class, 'index', $container))
                ->name('site/index'),
            Route::methods([Method::GET, Method::POST], '/contact')
                ->to(new ActionCaller(ContactController::class, 'contact', $container))
                ->name('site/contact'),
            Route::get('/test/{id:\w+}')
                ->to(new ActionCaller(SiteController::class, 'testParameter', $container))
                ->name('site/test'),
            Route::methods([Method::GET, Method::POST], '/login')
                ->to(new ActionCaller(AuthController::class, 'login', $container))
                ->name('site/login'),
            Route::get('/logout')
                ->to(new ActionCaller(AuthController::class, 'logout', $container))
                ->name('site/logout'),

            Route::get('/user')
                 ->to(new ActionCaller(UserController::class, 'index', $container))
                 ->name('user/index'),
            Route::get('/user/{login}')
                 ->to(new ActionCaller(UserController::class, 'profile', $container))
                 ->name('user/profile'),
        ];

        $router = (new RouterFactory(new FastRouteFactory(), $routes))($container);

        $router->addGroup('/blog', static function (RouteCollectorInterface $r) use (&$container) {
            $r->addRoute(
                Route::get('[/page{page:\d+}]')
                     ->to(new ActionCaller(BlogController::class, 'index', $container))
                     ->name('blog/index')
            );
            $r->addRoute(
                Route::get('/page/{slug}')
                     ->to(new ActionCaller(BlogController::class, 'page', $container))
                     ->name('blog/page')
            );
            $r->addRoute(
                Route::get('/tag/{label}')
                     ->to(new ActionCaller(BlogController::class, 'tag', $container))
                     ->name('blog/tag')
            );
        });

        return $router;
    }
}
