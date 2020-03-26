<?php


namespace App;


use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DataConverter implements MiddlewareInterface
{
    private ContainerInterface $container;

    private DataConverterInterface $dataConverter;

    public function __construct(ContainerInterface $container, DataConverterInterface $dataConverter)
    {
        $this->container = $container;
        $this->dataConverter = $dataConverter;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        if ($response instanceof DeferredResponse) {
            $response = $response->withDataConverter($this->dataConverter);
        }

        return $response;
    }
}
