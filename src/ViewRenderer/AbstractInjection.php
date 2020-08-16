<?php

declare(strict_types=1);

namespace App\ViewRenderer;

abstract class AbstractInjection implements InjectionInterface
{

    public function getContentParams(): array
    {
        return [];
    }

    public function getLayoutParams(): array
    {
        return [];
    }

    public function getMetaTags(): array
    {
        return [];
    }

    public function getLinkTags(): array
    {
        return [];
    }
}
