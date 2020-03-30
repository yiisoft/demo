<?php

declare(strict_types=1);

namespace App\Stream\Data;

final class JSONConverter implements Converter
{
    public function convert($data, array $params = []): string
    {
        // of course you can use JsonSerializer
        return json_encode($data, JSON_PRETTY_PRINT|JSON_INVALID_UTF8_IGNORE|JSON_UNESCAPED_UNICODE);
    }
}
