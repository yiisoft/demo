<?php

declare(strict_types=1);

namespace App\Stream;

use App\Stream\Data\Converter;
use Psr\Http\Message\StreamInterface;

final class DataStream implements StreamInterface
{
    /** @var mixed */
    private $data;
    private bool $readable = false;
    private ?int $size = null;
    private bool $rendered = false;
    private string $buffer = '';
    private int $caret = 0;

    /** @var string|null Class name */
    private ?string $converter = null;
    private array $converterParams = [];

    public function __construct($body)
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
        if (isset($this->data)) {
            $this->detach();
        }
    }
    public function detach()
    {
        $result = $this->data;
        unset($this->data);
        $this->size = null;
        $this->caret = 0;
        $this->buffer = '';
        $this->rendered = false;
        $this->readable = false;
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
        return $this->data === null || $this->rendered && $this->caret >= $this->size;
    }
    public function isSeekable(): bool
    {
        return $this->rendered;
    }
    public function seek($offset, $whence = \SEEK_SET): void
    {
        if (!$this->isSeekable()) {
            throw new \RuntimeException('Stream is not seekable.');
        }
        switch ($whence) {
            case SEEK_SET:
                $position = $whence;
                break;
            case SEEK_CUR:
                $position = $this->caret + $whence;
                break;
            case SEEK_END:
                $position = $this->size + $whence;
                break;
            default:
                throw new \InvalidArgumentException('Unsupported whence value.');
        }
        if ($position > $this->size || $position < 0) {
            throw new \RuntimeException('Impossible offset');
        }
        $this->caret = $position;
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
        if (!$this->isWritable()) {
            throw new \RuntimeException('Cannot write to a non-writable stream.');
        }
        return 0;
    }
    public function isReadable(): bool
    {
        return $this->readable;
    }
    public function read($length): string
    {
        if (!$this->isReadable()) {
            throw new \RuntimeException('Stream should be rendered.');
        }
        if ($this->eof()) {
            throw new \RuntimeException('Cannot read from ended stream.');
        }
        $result = substr($this->buffer, $this->caret, $length);
        $this->caret += strlen($result);
        return $result;
    }
    public function getContents(): string
    {
        if (!isset($this->data)) {
            throw new \RuntimeException('Unable to read stream contents.');
        }
        $content = '';
        while (!$this->eof()) {
            $content .= $this->read(PHP_INT_MAX);
        }
        return $content;
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

    public function getData()
    {
        return $this->data;
    }
    public function render(Converter $converter): void
    {
        $this->buffer = $converter->convert($this->data, $this->converterParams);
        $this->readable = true;
        $this->rendered = true;
        $this->caret = 0;
        $this->size = strlen($this->buffer);
    }
    public function setConverter(?string $converter, array $params = []): self
    {
        $this->converter = $converter;
        $this->converterParams = $params;
        return $this;
    }
    public function getConverter(): ?string
    {
        return $this->converter;
    }
}
