<?php
namespace Yiisoft\Yii\Demo\Factory;

use Psr\Container\ContainerInterface;
use Yiisoft\Router\FastRoute\FastRouteFactory;
use Yiisoft\Router\Route;
use Yiisoft\Router\RouterFactory;
use Yiisoft\Web\Middleware\Controller;
use Yiisoft\Yii\Demo\Controllers\SiteController;

class AppRouterFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $routes = [
            Route::get('/')->to(new Controller(SiteController::class, 'index', $container)),
            Route::get('/test/{id:\w+}')->to(new Controller(SiteController::class, 'testParameter', $container))
        ];

        return (new RouterFactory(new FastRouteFactory(), $routes))($container);
    }

    public static function __set_state(array $state): self
    {
        return new self();
    }
}
