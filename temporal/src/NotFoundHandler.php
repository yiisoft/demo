<?php

declare(strict_types=1);

namespace App;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class NotFoundHandler implements RequestHandlerInterface
{
    private ResponseFactoryInterface $responseFactory;
    private StreamFactoryInterface $streamFactory;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        StreamFactoryInterface   $streamFactory,
    )
    {
        $this->responseFactory = $responseFactory;
        $this->streamFactory = $streamFactory;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->responseFactory->createResponse(404)
            ->withBody(
                $this->streamFactory->createStream('Not found')
            )
            ->withHeader('Content-Type', 'text/html');
    }
}
