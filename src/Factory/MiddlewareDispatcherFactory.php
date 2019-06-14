<?php
namespace Yiisoft\Yii\Demo\Factory;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Yiisoft\Web\MiddlewareDispatcher;
use Yiisoft\Router\Middleware\Router;
use Yiisoft\Router\RouterInterface;

class MiddlewareDispatcherFactory
{
    public function __invoke(ContainerInterface $container)
    {
        /* @var ResponseFactoryInterface $responseFactory */
            $responseFactory = $container->get(ResponseFactoryInterface::class);

            /* @var Router $router */
            $router = $container->get(RouterInterface::class);

            return new MiddlewareDispatcher([
                new Router($router),
            ], $responseFactory);
    }

    public static function __set_state($state)
    {
        return new self();
    }
}
