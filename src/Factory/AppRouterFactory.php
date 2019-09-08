<?php
namespace App\Factory;

use App\Controller\AuthController;
use App\Controller\CycleController;
use Psr\Container\ContainerInterface;
use Yiisoft\Router\FastRoute\FastRouteFactory;
use Yiisoft\Router\Method;
use Yiisoft\Router\Route;
use Yiisoft\Router\RouterFactory;
use Yiisoft\Yii\Web\Middleware\ActionCaller;
use App\Controller\SiteController;
use App\Controller\ContactController;

class AppRouterFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $routes = [
            Route::get('/')->to(new ActionCaller(SiteController::class, 'index', $container)),
            Route::methods([Method::GET, Method::POST], '/contact')->to(new ActionCaller(ContactController::class, 'contact', $container)),
            Route::get('/test/{id:\w+}')->to(new ActionCaller(SiteController::class, 'testParameter', $container)),

            Route::methods([Method::GET, Method::POST], '/login')->to(new ActionCaller(AuthController::class, 'login', $container)),
            Route::get('/logout')->to(new ActionCaller(AuthController::class, 'logout', $container)),

            Route::get('/cycle/testConnection')->to(new ActionCaller(CycleController::class, 'testConnection', $container)),
        ];

        return (new RouterFactory(new FastRouteFactory(), $routes))($container);
    }
}
