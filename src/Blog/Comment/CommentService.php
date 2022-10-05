<?php

declare(strict_types=1);

namespace App\Blog\Comment;

use Yiisoft\Data\Paginator\KeysetPaginator;

final class CommentService
{
    public function __construct(private CommentRepository $repository)
    {
    }

    public function getFeedPaginator(): KeysetPaginator
    {
        return (new KeysetPaginator($this->repository->getReader()));
    }
}
