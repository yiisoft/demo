<?php

declare(strict_types=1);

namespace App\Blog\Archive;

use App\Blog\Entity\Post;
use App\Blog\Post\PostRepository;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Select;
use Cycle\Database\DatabaseInterface;
use Cycle\Database\Driver\DriverInterface;
use Cycle\Database\Driver\SQLite\SQLiteDriver;
use Cycle\Database\Injection\Fragment;
use Cycle\Database\Injection\FragmentInterface;
use Cycle\Database\Query\SelectQuery;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Cycle\Data\Reader\EntityReader;

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
     * @return DataReaderInterface Collection of Array('Count' => '123', 'Month' => '8', 'Year' => '2019') on read
     */
    public function getFullArchive(): DataReaderInterface
    {
        $sort = Sort::only(['year', 'month', 'count'])->withOrder(['year' => 'desc', 'month' => 'desc']);

        $query = $this
            ->select()
            ->buildQuery()
            ->columns([
                'count(post.id) count',
                $this->extractFromDateColumn('month'),
                $this->extractFromDateColumn('year'),
            ])
            ->groupBy('year, month');

        return (new EntityReader($query))->withSort($sort);
    }

    /**
     * @param string $attr Can be 'day', 'month' or 'year'
     *
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
     * @psalm-suppress UndefinedDocblockClass
     *
     * @param Select|SelectQuery $query
     *
     * @return EntityReader
     */
    private function prepareDataReader($query): EntityReader
    {
        return (new EntityReader($query))->withSort(Sort::only(['published_at'])->withOrder(['published_at' => 'desc']));
    }
}
