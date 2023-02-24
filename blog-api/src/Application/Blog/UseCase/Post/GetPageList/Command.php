<?php

declare(strict_types=1);

namespace App\Application\Blog\UseCase\Post\GetPageList;

final class Command
{
    public function __construct(
        private int $page,
    ) {
    }

    public function getPage(): int
    {
        return $this->page;
    }
}
