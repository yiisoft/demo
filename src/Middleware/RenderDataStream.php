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
    private array $converters = [];
    private ContainerInterface $container;
    private StreamFactoryInterface $streamFactory;

    public function __construct(ContainerInterface $container, StreamFactoryInterface $streamFactory)
    {
        $this->container = $container;
        $this->streamFactory = $streamFactory;
    }

    public function defineConverter(string $className, string $format, string $mime = null, bool $deferred = false): self
    {
        if (!is_subclass_of($className, Converter::class, true)) {
            throw new \InvalidArgumentException('Converter class should implement ' . Converter::class);
        }
        $this->converters[$format] = [$className, $mime, $deferred];
        return $this;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        $stream = $response->getBody();
        if (!$stream instanceof DataStream) {
            return $response;
        }

        $data = $stream->getData();

        $format = $this->getRelevantFormat($data->getFormat(), $request);
        [$className, $mime, $deferred] = $this->converters[$format];

        /** @var Converter $converter */
        $converter = $this->container->get($className);

        $response = $response->withBody(
            $deferred
                ? new GeneratorStream((fn () => yield $this->convertData($data, $converter))())
                : $this->convertData($data, $converter)
        );

        if ($mime !== null) {
            $response = $response->withHeader('Content-Type', $mime);
        }
        if ($data->getCode() !== null) {
            $response = $response->withStatus($data->getCode());
        }
        return $this->addHeaders($response, $data->getHeaders());
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
    private function getRelevantFormat(?string $format, ServerRequestInterface $request): string
    {
        if ($format !== null) {
            if (!array_key_exists($format, $this->converters)) {
                throw new \RuntimeException('Undefined format ' . $format);
            }
            return $format;
        }
        # todo: get type from header

        return array_key_first($this->converters);
    }
}
