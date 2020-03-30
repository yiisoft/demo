<?php

declare(strict_types=1);

namespace App\Stream\Data;

interface Converter
{
    public function convert($data, array $params = []): string;
}
