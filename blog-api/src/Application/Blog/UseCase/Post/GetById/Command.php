<?php

declare(strict_types=1);

namespace App\Application\Blog\UseCase\Post\GetById;

final class Command
{
    public function __construct(
        private int $id,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }
}
