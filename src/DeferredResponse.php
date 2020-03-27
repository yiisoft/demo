<?php

namespace App;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

class DeferredResponse implements ResponseInterface
{
    private ResponseInterface $response;

    private StreamFactoryInterface $streamFactory;

    private $data;

    private ?StreamInterface $dataStream = null;

    private ?DataConverterInterface $dataConverter = null;

    public function __construct($data, ResponseFactoryInterface $responseFactory, StreamFactoryInterface $streamFactory)
    {
        $this->response = $responseFactory->createResponse();
        $this->streamFactory = $streamFactory;
        $this->data = $data;
    }

    public function getBody()
    {
        if ($this->dataStream !== null) {
            return $this->dataStream;
        }

        if (is_callable($this->data)) {
            $this->data = ($this->data)();
        }

        if ($this->dataConverter !== null) {
            return $this->dataStream = $this->convertData();
        }

        if (is_string($this->data)) {
            return $this->dataStream = $this->streamFactory->createStream($this->data);
        }

        throw new \RuntimeException('Data must be a string value.');
    }

    public function getHeader($name)
    {
        return $this->response->getHeader($name);
    }

    public function getHeaderLine($name)
    {
        return $this->response->getHeaderLine($name);
    }

    public function getHeaders()
    {
        return $this->response->getHeaders();
    }

    public function getProtocolVersion()
    {
        return $this->response->getProtocolVersion();
    }

    public function getReasonPhrase()
    {
        return $this->response->getReasonPhrase();
    }

    public function getStatusCode()
    {
        return $this->response->getStatusCode();
    }

    public function hasHeader($name)
    {
        return $this->response->hasHeader($name);
    }

    public function withAddedHeader($name, $value)
    {
        $this->response = $this->response->withAddedHeader($name, $value);
        return clone $this;
    }

    public function withBody(StreamInterface $body)
    {
        $this->dataStream = $body;
        return clone $this;
    }

    public function withHeader($name, $value)
    {
        $this->response = $this->response->withHeader($name, $value);
        return clone $this;
    }

    public function withoutHeader($name)
    {
        $this->response = $this->response->withoutHeader($name);
        return clone $this;
    }

    public function withProtocolVersion($version)
    {
        $this->response = $this->response->withProtocolVersion($version);
        return clone $this;
    }

    public function withStatus($code, $reasonPhrase = '')
    {
        $this->response = $this->response->withStatus($code, $reasonPhrase);
        return clone $this;
    }

    public function withDataConverter(DataConverterInterface $dataConverter)
    {
        if ($this->dataConverter !== null) {
            return $this;
        }
        $this->dataConverter = $dataConverter;
        return clone $this;
    }

    private function convertData(): StreamInterface
    {
        return $this->dataConverter->convertData($this->data, $this->response);
    }
}
