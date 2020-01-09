<?php

namespace App\Repository;

use App\Entity\Post;
use Cycle\ORM\Iterator;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Select;
use Spiral\Database\Injection\Fragment;
use Spiral\Database\Query\SelectQuery;

class PostRepository extends Select\Repository
{
    public function __construct(ORMInterface $orm, $role = Post::class)
    {
        parent::__construct(new Select($orm, $role));
    }

    public function findLastPublic(array $load = [], int $start = 0, int $limit = 50): Iterator
    {
        return $this->select()
                    ->where(['public' => true])
                    ->orderBy('published_at', 'DESC')
                    ->offset($start)
                    ->limit($limit)
                    ->load($load)
                    ->getIterator();
    }

    public function findBySlug(string $slug, array $load = []): ?Post
    {
        return $this->select()
                    ->where(['slug' => $slug])
                    ->load($load)
                    ->fetchOne();
    }

    /**
     * @return array Array of Array('Count' => '123', 'Month' => '8', 'Year' => '2019')
     */
    public function getArchive(): array
    {
        /** @var Select|SelectQuery|Select\QueryBuilder $select */
        $select = $this->select();
        $data = $select
            ->columns([
                'count(post.id) Count',
                new Fragment('extract(month from post.published_at) Month'),
                new Fragment('extract(year from post.published_at) Year'),
            ])
            ->where(['public' => true])
            ->orderBy(new Fragment('Year'), 'DESC')
            ->orderBy(new Fragment('Month'), 'DESC')
            ->groupBy(new Fragment('Year, Month'))
            ->run()->fetchAll();
        return $data;
    }
}
