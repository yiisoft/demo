<?php


namespace App;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DataConverter implements MiddlewareInterface
{
    private DataConverterInterface $dataConverter;

    public function __construct(DataConverterInterface $dataConverter)
    {
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
