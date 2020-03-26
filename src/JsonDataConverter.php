<?php

declare(strict_types=1);

namespace App;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Yiisoft\Serializer\JsonSerializer;

final class JsonDataConverter implements DataConverterInterface
{
    private JsonSerializer $jsonSerializer;
    private StreamFactoryInterface $streamFactory;

    public function __construct(
        JsonSerializer $jsonSerializer,
        StreamFactoryInterface $streamFactory
    ) {
        $this->jsonSerializer = $jsonSerializer;
        $this->streamFactory = $streamFactory;
    }

    public function convertData($data, ResponseInterface &$response): StreamInterface
    {
        $content = $this->jsonSerializer->serialize($data);
        $response = $response->withHeader('Content-Type', $this->getContentType());
        return $this->streamFactory->createStream($content);
    }

    protected function getContentType(): string
    {
        return 'application/json';
    }
}
