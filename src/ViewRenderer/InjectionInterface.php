<?php

declare(strict_types=1);

namespace App\ViewRenderer;

interface InjectionInterface
{

    public function getContentParams(): array;

    public function getLayoutParams(): array;

    public function getMetaTags(): array;

    public function getLinkTags(): array;
}
