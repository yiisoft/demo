<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Stream\DataStream;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class SetStreamConverter implements MiddlewareInterface
{
    private string $converter;
    private array $params = [];

    public function __construct(string $converter, array $params = [])
    {
        $this->converter = $converter;
        $this->params = $params;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        $stream = $response->getBody();
        if ($stream instanceof DataStream) {
            $stream->setConverter($this->converter, $this->params);
        }
        return $response;
    }
}
