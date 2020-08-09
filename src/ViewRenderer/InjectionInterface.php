<?php

declare(strict_types=1);

namespace App\ViewRenderer;

interface InjectionInterface
{

    public function getParams(): array;

    public function getMetaTags(): array;

    public function getLinkTags(): array;
}
