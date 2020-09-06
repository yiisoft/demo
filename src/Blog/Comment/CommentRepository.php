<?php

declare(strict_types=1);

namespace App\Blog\Comment;

use Cycle\ORM\Select;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Cycle\DataReader\SelectDataReader;

final class CommentRepository extends Select\Repository
{
    public function getReader(): DataReaderInterface
    {
        return (new SelectDataReader($this->select()))
            ->withSort($this->getSort());
    }

    private function getSort(): Sort
    {
        return (new Sort([]))->withOrder(['id' => 'asc']);
    }
}
