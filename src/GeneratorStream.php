<?php

namespace App;

use Generator;
use Psr\Http\Message\StreamInterface;

class GeneratorStream implements StreamInterface
{
    private ?Generator $stream;

    private bool $seekable = false;

    private bool $readable = false;

    private bool $writable = false;

    private ?int $size = null;

    private int $caret = 0;

    private bool $started = false;

    public function __construct($body)
    {
        if ($body instanceof Generator) {
            $this->stream = $body;
            $this->seekable = false;
            $this->readable = true;
            $this->writable = false;
        } else {
            throw new \InvalidArgumentException('First argument must be a Generator.');
        }
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
        if (isset($this->stream)) {
            $this->detach();
        }
    }

    public function detach()
    {
        if (!isset($this->stream)) {
            return null;
        }
        $result = $this->stream;
        unset($this->stream);
        $this->size = null;
        $this->caret = 0;
        $this->started = false;
        $this->readable = $this->writable = $this->seekable = false;
        return $result;
    }

    public function getSize(): ?int
    {
        if (null !== $this->size) {
            return $this->size;
        }

        if (!isset($this->stream)) {
            return null;
        }

        return null;
    }

    public function tell(): int
    {
        return $this->caret;
    }

    public function eof(): bool
    {
        return $this->stream === null || !$this->stream->valid();
    }

    public function isSeekable(): bool
    {
        return $this->seekable;
    }

    public function seek($offset, $whence = \SEEK_SET): void
    {
        if (!$this->seekable) {
            throw new \RuntimeException('Stream is not seekable');
        }
    }

    public function rewind(): void
    {
        $this->stream->rewind();
        $this->caret = 0;
        $this->started = false;
    }

    public function isWritable(): bool
    {
        return $this->writable;
    }

    public function write($string): int
    {
        if (!$this->writable) {
            throw new \RuntimeException('Cannot write to a non-writable stream');
        }
        return 0;
    }

    public function isReadable(): bool
    {
        return $this->readable;
    }

    public function read($length): string
    {
        if (!$this->readable) {
            throw new \RuntimeException('Cannot read from non-readable stream');
        }
        $read = $this->started ? (string)$this->stream->send(null) : (string)$this->stream->current();
        $this->caret += strlen($read);
        if ($this->eof()) {
            $this->size = $this->caret;
        }
        $this->started = true;
        return $read;
    }

    public function getContents(): string
    {
        if (!isset($this->stream)) {
            throw new \RuntimeException('Unable to read stream contents');
        }

        return implode('', iterator_to_array($this->stream));
    }

    public function getMetadata($key = null)
    {
        if (!isset($this->stream)) {
            return $key ? null : [];
        }

        $meta = [
            'seekable' => $this->seekable,
            'eof' => $this->eof(),
        ];

        if (null === $key) {
            return $meta;
        }

        return $meta[$key] ?? null;
    }
}
