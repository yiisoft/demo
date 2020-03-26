<?php

namespace App;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\ResponseInterface;

interface DataConverterInterface
{
    public function convertData($data, ResponseInterface &$response): StreamInterface;
}
