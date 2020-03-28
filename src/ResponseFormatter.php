<?php


namespace App;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ResponseFormatter implements MiddlewareInterface
{
    private ResponseFormatterInterface $responseFormatter;

    public function __construct(ResponseFormatterInterface $responseFormatter)
    {
        $this->responseFormatter = $responseFormatter;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        if ($response instanceof Response && !$response->hasResponseFormatter()) {
            $response = $this->responseFormatter->format($response);
        }

        return $response;
    }
}
