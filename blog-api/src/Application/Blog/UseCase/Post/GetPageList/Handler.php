<?php

declare(strict_types=1);

namespace App\Application\Blog\UseCase\Post\GetPageList;

use App\Application\Blog\Service\BlogService;
use Yiisoft\Data\Paginator\PaginatorInterface;

final class Handler
{
    public function __construct(
        private BlogService $blogService,
    ) {
    }

    /**
     * TODO: Replace paginator with something else
     */
    public function handle(Command $command): PaginatorInterface
    {
        return $this->blogService->getPosts($command->getPage());
    }
}
