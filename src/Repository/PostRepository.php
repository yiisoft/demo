<?php

namespace App\Repository;

use App\Entity\Post;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Select;
use Spiral\Database\Injection\Fragment;
use Spiral\Database\Query\SelectQuery;
use Spiral\Pagination\PaginableInterface;

class PostRepository extends Select\Repository
{
    public function __construct(ORMInterface $orm, $role = Post::class)
    {
        parent::__construct(new Select($orm, $role));
    }

    public function findLastPublic(): PaginableInterface
    {
        return $this->select()
                    ->where(['public' => true])
                    ->orderBy('published_at', 'DESC')
                    ->load(['user', 'tags']);
    }

    public function findArchivedPublic(int $year, int $month): PaginableInterface
    {
        $begin = (new \DateTimeImmutable)->setDate($year, $month, 1)->setTime(0, 0, 0);
        $end = $begin->setDate($year, $month + 1, 1)->setTime(0, 0, -1);

        return $this->select()
                    ->where('public', true)
                    ->andWhere('published_at', 'between', $begin, $end)
                    ->orderBy('published_at', 'DESC')
                    ->load(['user', 'tags']);
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
        return $this->select()
            ->buildQuery()
            ->columns([
                'count(post.id) count',
                new Fragment('extract(month from post.published_at) month'),
                new Fragment('extract(year from post.published_at) year'),
            ])
            ->where(['public' => true])
            ->orderBy(new Fragment('year'), 'DESC')
            ->orderBy(new Fragment('month'), 'DESC')
            ->groupBy(new Fragment('year, month'))
            ->run()->fetchAll();
    }
}
