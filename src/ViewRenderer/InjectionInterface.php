<?php

declare(strict_types=1);

namespace App\ViewRenderer;

interface InjectionInterface
{

    public function withConfig(array $config): self;

    public function getParams(): array;
}
