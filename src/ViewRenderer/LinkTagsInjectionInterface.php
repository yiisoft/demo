<?php

declare(strict_types=1);

namespace App\ViewRenderer;

interface LinkTagsInjectionInterface
{
    public function getLinkTags(): array;
}
