<?php

declare(strict_types=1);

namespace App\Application\Blog\UseCase\Post\GetById;

use App\Application\Blog\Service\BlogService;

final class Handler
{
    public function __construct(
        private BlogService $blogService,
    ) {
    }

    public function handle(Command $command)
    {
        return $this->blogService->getPost($command->getId());
    }
}
