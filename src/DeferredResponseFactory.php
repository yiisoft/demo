<?php

namespace App;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

class DeferredResponseFactory implements ResponseFactoryInterface
{
    private ResponseFactoryInterface $responseFactory;

    private StreamFactoryInterface $streamFactory;

    public function __construct(ResponseFactoryInterface $responseFactory, StreamFactoryInterface $streamFactory)
    {
        $this->responseFactory = $responseFactory;
        $this->streamFactory = $streamFactory;
    }

    public function createResponse(int $code = 200, string $reasonPhrase = '', $data = null): ResponseInterface
    {
        return new DeferredResponse($data, $this->responseFactory, $this->streamFactory);
    }
}
