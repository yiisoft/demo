<?php

declare(strict_types=1);

namespace App\Blog\Comment;

use App\Blog\Entity\Comment;
use Cycle\ORM\Select;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Cycle\Data\Reader\EntityReader;

final class CommentRepository extends Select\Repository
{
    /**
     * @psalm-return DataReaderInterface<int, Comment>
     */
    public function getReader(): DataReaderInterface
    {
        return (new EntityReader($this->select()))
            ->withSort($this->getSort());
    }

    private function getSort(): Sort
    {
        return Sort::only(['id', 'public', 'created_at', 'post_id', 'user_id'])->withOrder(['id' => 'asc']);
    }
}
