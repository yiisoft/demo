<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yiisoft\Injector\Injector;

/**
 * ActionCaller maps a route to specified class instance and method.
 *
 * Dependencies are automatically injected into both method
 * and constructor based on types specified.
 */
final class ActionCaller implements MiddlewareInterface
{
    private string $class;
    private string $method;
    private ContainerInterface $container;

    public function __construct(string $class, string $method, ContainerInterface $container)
    {
        $this->class = $class;
        $this->method = $method;
        $this->container = $container;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $controller = $this->container->get($this->class);
        $result = (new Injector($this->container))->invoke([$controller, $this->method], [$request, $handler]);
        if ($result instanceof ResponseInterface) {
            return $result;
        }
        if ($result instanceof StreamInterface) {
            $stream = $result;
        } else {
            $stream = $this->container->get(\App\Stream\SmartStreamFactory::class)->createStream($result);
        }
        return $this->container->get(ResponseFactoryInterface::class)->createResponse()->withBody($stream);
    }
}
