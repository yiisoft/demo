<?php

namespace App\Repository;

use App\Constrain\PostDefault;
use App\Entity\Post;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Select;
use Spiral\Database\Injection\Fragment;
use Spiral\Pagination\PaginableInterface;

class PostRepository extends Select\Repository
{
    private ORMInterface $orm;

    public function __construct(ORMInterface $orm, $role = Post::class)
    {
        parent::__construct(new Select($orm, $role));
        $this->orm = $orm;
    }

    public function findLastPublic(): PaginableInterface
    {
        return $this->select()
                    ->constrain(new PostDefault())
                    ->load(['user', 'tags']);
    }

    public function findArchivedPublic(int $year, int $month): PaginableInterface
    {
        $begin = (new \DateTimeImmutable)->setDate($year, $month, 1)->setTime(0, 0, 0);
        $end = $begin->setDate($year, $month + 1, 1)->setTime(0, 0, -1);

        return $this->select()
                    ->constrain(new PostDefault())
                    ->andWhere('published_at', 'between', $begin, $end)
                    ->load(['user', 'tags']);
    }

    public function findByTag($tagId): PaginableInterface
    {
        return $this->select()
                    ->constrain(new PostDefault())
                    ->where(['tags.id' => $tagId])
                    ->load(['user']);
    }

    public function fullPostPage(string $slug, $userId = null): ?Post
    {
        $query = $this->select()
                      ->constrain(new PostDefault())
                      ->where(['slug' => $slug])
                      ->load('user', [
                          'method' => Select::SINGLE_QUERY,
                      ])
                      ->load('tags', [
                          'method' => Select::OUTER_QUERY,
                      ])
                      // ->load('comments.user') // eager loading
                      ->load('comments', [
                          'method' => Select::OUTER_QUERY,
                      ]);
        /** @var null|Post $post */
        $post = $query->fetchOne();
        // /** @var Select\Repository $commentRepo */
        // $commentRepo = $this->orm->getRepository(Comment::class);
        // $commentRepo->select()->load('user')->where('post_id', $post->getId())->fetchAll();
        return $post;
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
