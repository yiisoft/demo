<?php

declare(strict_types=1);

namespace App;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Yiisoft\Serializer\JsonSerializer;

final class JsonDataConverter implements DataConverterInterface
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

    public function convertData($data, ResponseInterface &$response): StreamInterface
    {
        $content = $this->jsonSerializer->serialize($data);
        $response = $response->withHeader('Content-Type', $this->contentType);

        return $this->streamFactory->createStream($content);
    }
}
