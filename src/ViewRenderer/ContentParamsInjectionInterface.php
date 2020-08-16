<?php

declare(strict_types=1);

namespace App\ViewRenderer;

interface ContentParamsInjectionInterface
{
    public function getContentParams(): array;
}
