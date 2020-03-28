<?php

declare(strict_types=1);

namespace App;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Yiisoft\Serializer\JsonSerializer;

final class JsonResponseFormatter implements ResponseFormatterInterface
{
    /**
     * @var string the Content-Type header for the response
     */
    private string $contentType = 'application/json';

    private JsonSerializer $jsonSerializer;

    private StreamFactoryInterface $streamFactory;

    public function __construct(
        JsonSerializer $jsonSerializer,
        StreamFactoryInterface $streamFactory
    ) {
        $this->jsonSerializer = $jsonSerializer;
        $this->streamFactory = $streamFactory;
    }

    public function format(Response $deferredResponse): ResponseInterface
    {
        $content = $this->jsonSerializer->serialize($deferredResponse->getData());
        $response = $deferredResponse->getResponse();
        $response->getBody()->write($content);

        return $response->withHeader('Content-Type', $this->contentType);
    }
}
