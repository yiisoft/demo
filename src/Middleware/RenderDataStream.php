<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Stream\Data\Converter;
use App\Stream\DataStream;
use App\Stream\GeneratorStream;
use App\Stream\Value\DataResponseProvider;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class RenderDataStream implements MiddlewareInterface
{
    public string $defaultFormat = 'text/html';
    public array $converters = [];
    public bool $deferred = false;

    private ContainerInterface $container;
    private StreamFactoryInterface $streamFactory;

    public function __construct(ContainerInterface $container, StreamFactoryInterface $streamFactory)
    {
        $this->container = $container;
        $this->streamFactory = $streamFactory;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        $stream = $response->getBody();
        if (!$stream instanceof DataStream) {
            return $response;
        }

        $data = $stream->getData();
        $format = $data->getFormat() ?? $this->getRelevantType($request);

        if (!array_key_exists($format, $this->converters)) {
            throw new \Exception('Undefined format ' . $format);
        }

        $converter = $this->getConverter($this->converters[$format]);

        if ($this->deferred) {
            $response = $response->withBody(
                new GeneratorStream((fn () => yield $this->convertData($data, $converter))())
            );
        } else {
            $response = $response->withBody($this->convertData($data, $converter));
        }

        if ($data->getCode() !== null) {
            $response = $response->withStatus($data->getCode());
        }

        return $this->addHeaders($response->withHeader('Content-Type', $format), $data->getHeaders());
    }

    private function addHeaders(ResponseInterface $response, array $headers): ResponseInterface
    {
        foreach ($headers as $header => $value) {
            $response = $response->withHeader($header, $value);
        }
        return $response;
    }
    private function convertData(DataResponseProvider $data, Converter $converter): StreamInterface
    {
        $result = $converter->convert($data->getData(), $data->getParams());
        return $this->streamFactory->createStream($result);
    }
    private function getRelevantType(ServerRequestInterface $request): string
    {
        # todo
        return $this->defaultFormat;
    }
    private function getConverter(?string $format): Converter
    {
        return $this->container->get($format);
    }
}
