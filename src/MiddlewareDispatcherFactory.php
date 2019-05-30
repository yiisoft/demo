<?php
namespace Yiisoft\Yii\Demo;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use yii\di\Container;
use yii\web\MiddlewareDispatcher;
use Yiisoft\Router\Middleware\Router;
use Yiisoft\Router\Route;
use Yiisoft\Router\RouterInterface;

class MiddlewareDispatcherFactory
{
    public function __invoke(Container $container)
    {
        /* @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $container->get(ResponseFactoryInterface::class);

        /* @var Router $router */
        $router = $container->get(RouterInterface::class);

        $router->addRoute(Route::get('/')->to(function (ServerRequestInterface $request, RequestHandlerInterface $next) use ($responseFactory) {
            $response = $responseFactory->createResponse();
            $response->getBody()->write('You are at homepage.');
            return $response;
        }));

        $router->addRoute(Route::get('/test/{id:\w+}')->to(function (ServerRequestInterface $request, RequestHandlerInterface $next) use ($responseFactory) {
            $id = $request->getAttribute('id');

            $response = $responseFactory->createResponse();
            $response->getBody()->write('You are at test with param ' . $id);
            return $response;
        }));

        return new MiddlewareDispatcher([
            new Router($router),
        ], $responseFactory);
    }
}