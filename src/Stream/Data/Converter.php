<?php

declare(strict_types=1);

namespace App\Stream\Data;

interface Converter
{
    public static function getFormat(): string;
    public function convert($data, array $params = []): string;
}
