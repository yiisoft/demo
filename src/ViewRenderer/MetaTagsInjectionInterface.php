<?php

declare(strict_types=1);

namespace App\ViewRenderer;

interface MetaTagsInjectionInterface
{
    public function getMetaTags(): array;
}
