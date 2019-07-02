<?php
namespace App\Factory;

use Psr\Container\ContainerInterface;
use Yiisoft\Router\FastRoute\FastRouteFactory;
use Yiisoft\Router\Route;
use Yiisoft\Router\RouterFactory;
use Yiisoft\Yii\Web\Middleware\Controller;
use App\Controller\SiteController;
use Middlewares\BasicAuthentication;
use Middlewares\DigestAuthentication;
use App\Http\MiddlewareChain;

class AppRouterFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $basicAuth = $container->get(BasicAuthentication::class);
        $digestAuth = $container->get(DigestAuthentication::class);
        $authorizedAction = new Controller(SiteController::class, 'auth', $container);

        $routes = [
            Route::get('/')->to(new Controller(SiteController::class, 'index', $container)),
            Route::get('/test/{id:\w+}')->to(new Controller(SiteController::class, 'testParameter', $container)),
            Route::get('/basic-auth')->to(new MiddlewareChain($basicAuth, $authorizedAction)),
            Route::get('/digest-auth')->to(new MiddlewareChain($digestAuth, $authorizedAction)),
        ];

        return (new RouterFactory(new FastRouteFactory(), $routes))($container);
    }

    public static function __set_state(array $state): self
    {
        return new self();
    }
}
