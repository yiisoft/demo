<?php

declare(strict_types=1);

namespace App;

class VersionProvider
{
    public function __construct(public $version = '3.0')
    {
    }
}
