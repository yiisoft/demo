<?php

declare(strict_types=1);

namespace App\Blog\Archive;

use App\Blog\Entity\Post;
use App\Blog\Post\PostRepository;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Select;
use Spiral\Database\DatabaseInterface;
use Spiral\Database\Driver\DriverInterface;
use Spiral\Database\Driver\SQLite\SQLiteDriver;
use Spiral\Database\Injection\Expression;
use Spiral\Database\Injection\Fragment;
use Spiral\Database\Injection\FragmentInterface;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Cycle\DataReader\SelectDataReader;

/**
 * This repository is not associated with Post entity
 */
final class ArchiveRepository
{
    private PostRepository $postRepo;

    public function __construct(ORMInterface $orm)
    {
        /** @var PostRepository $postRepo */
        $postRepo = $orm->getRepository(Post::class);
        $this->postRepo = $postRepo;
    }

    public function select(): Select
    {
        return $this->postRepo->select();
    }

    /**
     * @param int $year
     * @param int $month
     * @return SelectDataReader
     * @throws \Exception
     */
    public function getMonthlyArchive(int $year, int $month): DataReaderInterface
    {
        $begin = (new \DateTimeImmutable())->setDate($year, $month, 1)->setTime(0, 0, 0);
        $end = $begin->setDate($year, $month + 1, 1)->setTime(0, 0, -1);

        $query = $this->select()
                    ->andWhere('published_at', 'between', $begin, $end)
                    ->load(['user', 'tags']);
        return $this->prepareDataReader($query);
    }

    public function getYearlyArchive(int $year): DataReaderInterface
    {
        $begin = (new \DateTimeImmutable())->setDate($year, 1, 1)->setTime(0, 0, 0);
        $end = $begin->setDate($year + 1, 1, 1)->setTime(0, 0, -1);

        $query = $this
            ->select()
            ->andWhere('published_at', 'between', $begin, $end)
            ->load('user', ['method' => Select::SINGLE_QUERY])
            ->orderBy(['published_at' => 'asc']);
        return $this->prepareDataReader($query);
    }

    /**
     * @return SelectDataReader Collection of Array('Count' => '123', 'Month' => '8', 'Year' => '2019')
     */
    public function getFullArchive(): DataReaderInterface
    {
        $sort = (new Sort([]))->withOrder(['year' => 'desc', 'month' => 'desc']);

        $query = $this
            ->select()
            ->buildQuery()
            ->columns([
                'count(post.id) count',
                $this->extractFromDateColumn('month'),
                $this->extractFromDateColumn('year'),
            ])
            ->groupBy('year, month');

        return (new SelectDataReader($query))->withSort($sort);
    }

    /**
     * @param string $attr Can be 'day', 'month' or 'year'
     * @return FragmentInterface
     */
    private function extractFromDateColumn(string $attr): FragmentInterface
    {
        $driver = $this->getDriver();
        $wrappedField = $driver->getQueryCompiler()->quoteIdentifier($attr);
        if ($driver instanceof SQLiteDriver) {
            $str = ['year' => '%Y', 'month' => '%m', 'day' => '%d'][$attr];
            return new Fragment("strftime('{$str}', post.published_at) {$wrappedField}");
        }
        return new Fragment("extract({$attr} from post.published_at) {$wrappedField}");
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

    /**
     * @param Select|SelectQuery $query
     * @return SelectDataReader
     */
    private function prepareDataReader($query): SelectDataReader
    {
        return (new SelectDataReader($query))->withSort((new Sort([]))->withOrder(['published_at' => 'desc']));
    }
}
