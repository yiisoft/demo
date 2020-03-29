<?php

declare(strict_types=1);

namespace App\Stream\Data;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface Converter
{
    public static function getFormat(): string;
    /**
     * @param MessageInterface $message
     * @return ResponseInterface|ServerRequestInterface|RequestInterface
     */
    public function setHeaders(MessageInterface $message): MessageInterface;
    public function convert($data, array $params = []): string;
}
