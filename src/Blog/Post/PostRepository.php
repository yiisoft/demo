<?php

namespace App\Blog\Post;

use App\Blog\Comment\Scope\PublicScope;
use App\Blog\Entity\Post;
use Cycle\ORM\Select;
use Spiral\Database\DatabaseInterface;
use Spiral\Database\Driver\DriverInterface;
use Spiral\Database\Injection\Fragment;
use Yiisoft\Yii\Cycle\DataReader\SelectDataReader;

class PostRepository extends Select\Repository
{
    public function findAllPreloaded(): SelectDataReader
    {
        $query = $this->select()
                ->load(['user', 'tags']);
        return new SelectDataReader($query);
    }

    public function findArchivedPublic(int $year, int $month): SelectDataReader
    {
        $begin = (new \DateTimeImmutable())->setDate($year, $month, 1)->setTime(0, 0, 0);
        $end = $begin->setDate($year, $month + 1, 1)->setTime(0, 0, -1);

        $query = $this->select()
                    ->andWhere('published_at', 'between', $begin, $end)
                    ->load(['user', 'tags']);
        return new SelectDataReader($query);
    }

    public function findByTag($tagId): SelectDataReader
    {
        $query = $this->select()
                    ->where(['tags.id' => $tagId])
                    ->load(['user']);
        return new SelectDataReader($query);
    }

    public function fullPostPage(string $slug, ?string $userId = null): ?Post
    {
        $query = $this->select()
                      ->where(['slug' => $slug])
                      ->load('user', [
                          'method' => Select::SINGLE_QUERY,
                      ])
                      ->load('tags', [
                          'method' => Select::OUTER_QUERY,
                      ])
                      // force loading in single query with comments
                      ->load('comments.user', ['method' => Select::SINGLE_QUERY])
                      ->load('comments', [
                          'method' => Select::OUTER_QUERY,
                          // not works (default Constraint would not be replaced):
                          'load' => new PublicScope($userId === null ? null : ['user_id' => $userId]),
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
        try {
            if ($this->getDriver() instanceof \Spiral\Database\Driver\SQLite\SQLiteDriver) {
                return $this->select()
                            ->buildQuery()
                            ->columns(
                                [
                                    'count(post.id) count',
                                    new Fragment('strftime(\'%m\', post.published_at) month'),
                                    new Fragment('strftime(\'%Y\', post.published_at) year'),
                                ]
                            )
                            ->orderBy('year', 'DESC')
                            ->orderBy('month', 'DESC')
                            ->groupBy('year, month')
                            ->fetchAll();
            }
            return $this->select()
                        ->buildQuery()
                        ->columns(
                            [
                                'count(post.id) count',
                                new Fragment('extract(month from post.published_at) month'),
                                new Fragment('extract(year from post.published_at) year'),
                            ]
                        )
                        ->orderBy('year', 'DESC')
                        ->orderBy('month', 'DESC')
                        ->groupBy('year, month')
                        ->fetchAll();
        } catch (\Spiral\Database\Exception\StatementException $d) {
            return [];
        }
    }

    private function getDriver(): DriverInterface
    {
        return $this->select()
                    ->getBuilder()
                    ->getLoader()
                    ->getSource()
                    ->getDatabase()
                    ->getDriver(DatabaseInterface::READ);
    }
}
