<?php

declare(strict_types=1);

namespace App\Stream;

use App\Stream\Value\DataResponseProvider;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

final class SmartStreamFactory
{
    private StreamFactoryInterface $defaultFactory;

    public function __construct(StreamFactoryInterface $defaultFactory)
    {
        $this->defaultFactory = $defaultFactory;
    }

    public function createStream($data): StreamInterface
    {
        if (is_string($data)) {
            return $this->defaultFactory->createStream($data);
        }
        if ($data instanceof \SplFileInfo) {
            return $this->defaultFactory->createStream($data->getPath());
        }
        if (is_resource($data)) {
            return $this->defaultFactory->createStreamFromResource($data);
        }
        if ($data instanceof \Generator) {
            return new \App\Stream\GeneratorStream($data);
        }
        if ($data instanceof DataResponseProvider) {
            return new \App\Stream\DataStream($data);
        }
        return new \App\Stream\DataStream(new DataResponseProvider($data));
    }
}
