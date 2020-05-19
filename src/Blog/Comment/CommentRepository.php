<?php

declare(strict_types=1);

namespace App\Blog\Comment;

use Cycle\ORM\Select;
use Yiisoft\Data\Reader\Sort;

final class CommentRepository extends Select\Repository
{
    public function getReader(): CommentFeedReader
    {
        return (new CommentFeedReader($this->select()))
            ->withSort($this->getSort());
    }

    private function getSort(): Sort
    {
        return (new Sort([]))->withOrder(['id' => 'asc']);
    }
}
