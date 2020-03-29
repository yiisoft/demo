<?php

declare(strict_types=1);

namespace App\Stream\Data;

use Psr\Http\Message\MessageInterface;

class PrintRConverter implements Converter
{
    public static function getFormat(): string
    {
        return 'text/plain';
    }
    public function setHeaders(MessageInterface $message): MessageInterface
    {
        return $message->withHeader('Content-Type', 'text/print_r');
    }
    public function convert($data, array $params = []): string
    {
        return print_r($data, true);
    }
}
