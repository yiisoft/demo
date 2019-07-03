<?php
namespace App\Factory;

use Psr\Container\ContainerInterface;
use Yiisoft\Router\FastRoute\FastRouteFactory;
use Yiisoft\Router\Route;
use Yiisoft\Router\RouterFactory;
use Yiisoft\Yii\Web\Middleware\ActionCaller;
use App\Controller\SiteController;

class AppRouterFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $routes = [
            Route::get('/')->to(new ActionCaller(SiteController::class, 'index', $container)),
            Route::get('/test/{id:\w+}')->to(new ActionCaller(SiteController::class, 'testParameter', $container))
        ];

        return (new RouterFactory(new FastRouteFactory(), $routes))($container);
    }
}
