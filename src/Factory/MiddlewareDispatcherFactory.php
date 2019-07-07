<?php
namespace App\Factory;

use Psr\Container\ContainerInterface;
use Yiisoft\Router\Middleware\Router;
use Yiisoft\Yii\Web\MiddlewareDispatcher;

class MiddlewareDispatcherFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $router = $container->get(Router::class);

        return new MiddlewareDispatcher([$router], $container);
    }
}
