<?php

declare(strict_types=1);

namespace App\Blog\Comment;

use App\Blog\Entity\Comment;
use Cycle\ORM\Select;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Data\Cycle\Reader\EntityReader;

final class CommentRepository extends Select\Repository
{
    public function __construct(Select $select)
    {
        parent::__construct($select);
    }

    /**
     * @psalm-return DataReaderInterface<int, Comment>
     */
    public function getReader(): DataReaderInterface
    {
        return (new EntityReader($this->select()))
            ->withSort($this->getSort());
    }

    public function getSort(): Sort
    {
        return Sort::only(['id', 'public', 'created_at', 'post_id', 'user_id'])->withOrder(['id' => 'asc']);
    }

    public function findAll(array $scope = [], array $orderBy = []): DataReaderInterface
    {
        return new EntityReader($this
            ->select()
            ->where($scope)
            ->orderBy($orderBy));
    }
}
