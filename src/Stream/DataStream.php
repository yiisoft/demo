<?php

declare(strict_types=1);

namespace App\Stream;

use App\Stream\Value\DataResponseProvider;
use Psr\Http\Message\StreamInterface;

final class DataStream implements StreamInterface
{
    private DataResponseProvider $data;
    private bool $readable = false;
    private ?int $size = null;
    private bool $seekable = false;
    private int $caret = 0;

    public function __construct(DataResponseProvider $body)
    {
        $this->data = $body;
    }
    public function __toString(): string
    {
        try {
            if ($this->isSeekable()) {
                $this->seek(0);
            }
            return $this->getContents();
        } catch (\Exception $e) {
            return '';
        }
    }
    public function close(): void
    {
        if ($this->data !== null) {
            $this->detach();
        }
    }
    public function detach()
    {
        $result = $this->data;
        $this->data = null;
        return $result;
    }
    public function getSize(): ?int
    {
        return $this->size;
    }
    public function tell(): int
    {
        return $this->caret;
    }
    public function eof(): bool
    {
        return $this->data === null;
    }
    public function isSeekable(): bool
    {
        return $this->seekable;
    }
    public function seek($offset, $whence = \SEEK_SET): void
    {
        throw new \RuntimeException('Stream is not seekable.');
    }
    public function rewind(): void
    {
        $this->caret = 0;
    }
    public function isWritable(): bool
    {
        return false;
    }
    public function write($string): int
    {
        throw new \RuntimeException('Cannot write to a non-writable stream.');
    }
    public function isReadable(): bool
    {
        return $this->readable;
    }
    public function read($length): string
    {
        throw new \RuntimeException('Stream should be rendered.');
    }
    public function getContents(): string
    {
        return $this->read(PHP_INT_MAX);
    }
    public function getMetadata($key = null)
    {
        if (!isset($this->data)) {
            return $key ? null : [];
        }

        $meta = [
            'seekable' => $this->isSeekable(),
            'eof' => $this->eof(),
        ];

        if (null === $key) {
            return $meta;
        }

        return $meta[$key] ?? null;
    }

    public function getData(): DataResponseProvider
    {
        return $this->data;
    }
}
