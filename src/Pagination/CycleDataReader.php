<?php

declare(strict_types=1);

namespace App\Pagination;

use Countable;
use Cycle\ORM\Select;
use Spiral\Database\Query\QueryInterface;
use Yiisoft\Data\Reader\CountableDataInterface;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\OffsetableDataInterface;

class CycleDataReader implements DataReaderInterface, OffsetableDataInterface, CountableDataInterface
{
    /** @var QueryInterface|Select */
    private $query;
    private ?int $limit = null;
    private ?int $offset = null;
    private object $cache;

    /**
     * @param Select|QueryInterface $query
     */
    public function __construct($query)
    {
        if (!$query instanceof Countable) {
            throw new InvalidArgumentException('Query should implement Countable interface');
        }
        $this->query = clone $query;
        $this->cache = (object) ['count' => null];
    }
    public function withLimit(int $limit): self
    {
        $clone = clone $this;
        $clone->limit = $limit;
        return $clone;
    }
    public function withOffset(int $offset): self
    {
        $clone = clone $this;
        $clone->offset = $offset;
        return $clone;
    }
    public function count(): int
    {
        if ($this->cache->count === null) {
            $this->cache->count = $this->query->count();
        }
        return $this->cache->count;
    }
    public function read(): iterable
    {
        $this->count();
        $newQuery = clone $this->query;
        if ($this->offset !== null) {
            $newQuery->offset($this->offset);
        }
        if ($this->limit !== null) {
            $newQuery->limit($this->limit);
        }
        return $newQuery->fetchAll();
    }
}
