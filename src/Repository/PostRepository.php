<?php

namespace App\Repository;

use App\Entity\Post;
use Cycle\ORM\Iterator;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Select;

class PostRepository extends Select\Repository
{
    public function __construct(ORMInterface $orm, $role = Post::class)
    {
        parent::__construct(new Select($orm, $role));
    }

    public function findLastPublic(int $limit = 10, int $start = 0): Iterator
    {
        return $this->select()
                    ->where(['public' => true])
                    ->orderBy('published_at', 'DESC')
                    ->offset($start)
                    ->limit($limit)
                    ->load('user')
                    ->getIterator();
    }

    public function findBySlug(string $slug): ?Post
    {
        return $this->select()
                    ->where(['slug' => $slug])
                    ->load('user')
                    ->fetchOne();
    }
}
