<?php

declare(strict_types=1);

namespace App\Blog\Post;

use App\Blog\Entity\Post;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Select;
use Cycle\ORM\Transaction;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Cycle\DataReader\SelectDataReader;
use Throwable;

final class PostRepository extends Select\Repository
{
    private ORMInterface $orm;

    public function __construct(Select $select, ORMInterface $orm)
    {
        $this->orm = $orm;
        parent::__construct($select);
    }

    /**
     * Get posts without filter with preloaded Users and Tags
     */
    public function findAllPreloaded(): DataReaderInterface
    {
        $query = $this->select()
            ->load(['user', 'tags']);
        return $this->prepareDataReader($query);
    }

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

    /**
     * @param Post $post
     * @throws Throwable
     */
    public function save(Post $post): void
    {
        $transaction = new Transaction($this->orm);
        $transaction->persist($post);
        $transaction->run();
    }

    private function prepareDataReader($query): SelectDataReader
    {
        return (new SelectDataReader($query))->withSort((new Sort([]))->withOrder(['published_at' => 'desc']));
    }
}
