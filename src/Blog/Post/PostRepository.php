<?php

namespace App\Blog\Post;

use App\Blog\Comment\Scope\PublicScope;
use App\Blog\Entity\Post;
use Cycle\ORM\Select;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Cycle\DataReader\SelectDataReader;

final class PostRepository extends Select\Repository
{
    /**
     * Get posts without filter with preloaded Users and Tags
     * @return SelectDataReader
     */
    public function findAllPreloaded(): DataReaderInterface
    {
        $query = $this->select()
                ->load(['user', 'tags']);
        return $this->prepareDataReader($query);
    }

    /**
     * @param int|string $tagId
     * @return SelectDataReader
     */
    public function findByTag($tagId): DataReaderInterface
    {
        $query = $this
            ->select()
            ->where(['tags.id' => $tagId])
            ->load('user', ['method' => Select::SINGLE_QUERY]);
        return $this->prepareDataReader($query);
    }

    public function fullPostPage(string $slug): ?Post
    {
        $query = $this
            ->select()
            ->where(['slug' => $slug])
            ->load('user', ['method' => Select::SINGLE_QUERY])
            ->load(['tags'])
            // force loading in single query with comments
            ->load('comments.user', ['method' => Select::SINGLE_QUERY])
            ->load('comments', ['method' => Select::OUTER_QUERY]);
        /** @var null|Post $post */
        $post = $query->fetchOne();
        return $post;
    }

    private function prepareDataReader($query): SelectDataReader
    {
        return (new SelectDataReader($query))->withSort((new Sort([]))->withOrder(['published_at' => 'desc']));
    }
}
