<?php

namespace App;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

class DeferredResponseFactory implements ResponseFactoryInterface
{
    private StreamFactoryInterface $streamFactory;

    public function __construct(StreamFactoryInterface $streamFactory)
    {
        $this->streamFactory = $streamFactory;
    }

    public function createResponse(int $code = 200, string $reasonPhrase = '', $data = null): ResponseInterface
    {
        return new DeferredResponse($data, $this->createResponseInternal(), $this->streamFactory);
    }

    private function createResponseInternal(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        if (2 > \func_num_args()) {
            // This will make the Response class to use a custom reasonPhrase
            $reasonPhrase = null;
        }

        return new Response($code, [], null, '1.1', $reasonPhrase);
    }
}
