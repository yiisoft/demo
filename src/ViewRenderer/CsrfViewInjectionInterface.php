<?php

declare(strict_types=1);

namespace App\ViewRenderer;

interface CsrfViewInjectionInterface
{
    public function withRequestAttribute(?string $requestAttribute = null): CsrfViewInjectionInterface;
}
