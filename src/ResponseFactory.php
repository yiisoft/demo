<?php

namespace App;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

class ResponseFactory implements ResponseFactoryInterface
{
    private StreamFactoryInterface $streamFactory;

    private ResponseFactoryInterface $responseFactory;

    public function __construct(ResponseFactoryInterface $responseFactory, StreamFactoryInterface $streamFactory)
    {
        $this->streamFactory = $streamFactory;
        $this->responseFactory = $responseFactory;
    }

    public function createResponse(int $code = 200, string $reasonPhrase = '', $data = null): ResponseInterface
    {
        return new Response($data, $this->responseFactory->createResponse($code, $reasonPhrase), $this->streamFactory);
    }
}
