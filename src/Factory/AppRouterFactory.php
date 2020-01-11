<?php
namespace App\Factory;

use App\Controller\AuthController;
use App\Controller\ContactController;
use App\Controller\SiteController;
use Psr\Container\ContainerInterface;
use Yiisoft\Router\FastRoute\FastRouteFactory;
use Yiisoft\Http\Method;
use Yiisoft\Router\Route;
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
        ];

        return (new RouterFactory(new FastRouteFactory(), $routes))($container);
    }
}
