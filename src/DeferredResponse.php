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

    private ?ResponseFormatterInterface $responseFormatter = null;

    public function __construct($data, ResponseInterface $response, StreamFactoryInterface $streamFactory)
    {
        $this->response = $response;
        $this->streamFactory = $streamFactory;
        $this->data = $data;
    }

    public function getBody()
    {
        if ($this->dataStream !== null) {
            return $this->dataStream;
        }

        if ($this->responseFormatter !== null) {
            $this->response = $this->responseFormatter->format($this);
            return $this->dataStream = $this->response->getBody();
        }

        $data = $this->getData();
        if (is_string($data)) {
            return $this->dataStream = $this->streamFactory->createStream($data);
        }

        if ($data === null) {
            return $this->dataStream = $this->streamFactory->createStream();
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
        $response = clone $this;
        $response->response = $this->response->withAddedHeader($name, $value);
        return $response;
    }

    public function withBody(StreamInterface $body)
    {
        $response = clone $this;
        $response->dataStream = $body;
        return $response;
    }

    public function withHeader($name, $value)
    {
        $response = clone $this;
        $response->response = $this->response->withHeader($name, $value);
        return $response;
    }

    public function withoutHeader($name)
    {
        $response = clone $this;
        $response->response = $this->response->withoutHeader($name);
        return $response;
    }

    public function withProtocolVersion($version)
    {
        $response = clone $this;
        $response->response = $this->response->withProtocolVersion($version);
        return $response;
    }

    public function withStatus($code, $reasonPhrase = '')
    {
        $response = clone $this;
        $response->response = $this->response->withStatus($code, $reasonPhrase);
        return $response;
    }

    public function withResponseFormatter(ResponseFormatterInterface $responseFormatter): self
    {
        $response = clone $this;
        $response->responseFormatter = $responseFormatter;
        return $response;
    }

    public function withData($data): self
    {
        $response = clone $this;
        $response->data = $data;

        return $response;
    }

    public function hasResponseFormatter(): bool
    {
        return $this->responseFormatter !== null;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    public function getData()
    {
        if (is_callable($this->data)) {
            $this->data = ($this->data)();
        }
        return is_object($this->data) ? clone $this->data : $this->data;
    }
}
