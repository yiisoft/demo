<?php

declare(strict_types=1);

namespace App\Blog\Comment;

use Cycle\ORM\Select;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Filter\CompareFilter;
use Yiisoft\Data\Reader\Filter\FilterInterface;
use Yiisoft\Data\Reader\Filter\FilterProcessorInterface;
use Yiisoft\Data\Reader\FilterableDataInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Data\Reader\SortableDataInterface;

final class CommentFeedReader implements FilterableDataInterface, DataReaderInterface, SortableDataInterface
{
    private Select $query;
    private ?int $limit = null;
    private ?Sort $sorting = null;
    private ?FilterInterface $filter = null;

    public function __construct(Select $query)
    {
        $this->query = clone $query;
    }

    public function getSort(): ?Sort
    {
        return $this->sorting;
    }

    public function withLimit(int $limit): self
    {
        $clone = clone $this;
        $clone->limit = $limit;
        return $clone;
    }

    public function withSort(?Sort $sorting): self
    {
        $clone = clone $this;
        $clone->sorting = $sorting;
        return $clone;
    }

    public function read(): iterable
    {
        $query = clone $this->query;
        if ($this->sorting !== null) {
            $query->orderBy($this->sorting->getOrder());
        }
        if ($this->limit !== null) {
            $query->limit($this->limit);
        }

        if ($this->filter !== null) {
            $filterParams = $this->filter->toArray();
            $query->where($filterParams[1], $filterParams[0], $filterParams[2]);
        }

        return $query->fetchData();
    }

    public function readOne()
    {
        return (static function (iterable $data): \Generator {
            yield from $data;
        })($this->withLimit(1)->read())->current();
    }

    public function withFilter(FilterInterface $filter): self
    {
        if (!$filter instanceof CompareFilter) {
            throw new \InvalidArgumentException('Filter should implement CompareFilter');
        }

        $clone = clone $this;
        $clone->filter = $filter;
        return $clone;
    }

    public function withFilterProcessors(FilterProcessorInterface ...$filterProcessors): void
    {
        // skip
    }
}
