<?php


namespace App;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DeferredResponseFormatter implements MiddlewareInterface
{
    private ResponseFormatterInterface $responseFormatter;

    public function __construct(ResponseFormatterInterface $responseFormatter)
    {
        $this->responseFormatter = $responseFormatter;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        if ($response instanceof DeferredResponse) {
            $response = $response->withResponseFormatter($this->responseFormatter);
        }

        return $response;
    }
}
