<?php

declare(strict_types=1);

namespace App\ViewRenderer;

interface LayoutParamsInjectionInterface
{
    public function getLayoutParams(): array;
}
