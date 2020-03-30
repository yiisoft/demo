<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Stream\Data\Converter;
use App\Stream\Data\PrintRConverter;
use App\Stream\DataStream;
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
        $converter = $this->getConverter($data->getFormat(), $request);

        $response = $response->withBody($this->convertData($data, $converter));

        return $this->addHeaders($response->withHeader('Content-Type', $converter::getFormat()), $data->getHeaders());
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
        return PrintRConverter::class;
    }
    private function getConverter(?string $format, ServerRequestInterface $request): Converter
    {
        return $this->container->get($format ?? $this->getRelevantType($request));
    }
}
