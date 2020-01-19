<?php

namespace App;

use Closure;
use Countable;
use Cycle\ORM\Select;
use IteratorAggregate;
use Spiral\Database\Query\QueryInterface;
use Spiral\Pagination\PaginableInterface;

class CycleDataPaginator implements DataPaginatorInterface
{
    private int $count;
    private int $pagesCount = 1;
    private int $limit;
    private int $currentPage = 1;
    private array $pageTokens = [];
    private ?Closure $tokenGenerator = null;
    private object $dataQuery;

    /**
     * CycleDataPaginator constructor.
     * @param Select|QueryInterface $query
     * @param int $limit
     * @param int $count
     */
    public function __construct($query, int $limit = 25, int $count = 0)
    {
        $this->dataQuery = $query;
        if ($count > 0) {
            $this->count = $count;
        } elseif ($query instanceof Countable) {
            $this->count = $query->count();
        }
        $this->limit = max($limit, 1);
        $this->calcPages();
    }

    public function read(): iterable
    {
        $this->paginate();
        if ($this->dataQuery instanceof IteratorAggregate) {
            return $this->dataQuery->getIterator();
        }
        return $this->dataQuery->fetchAll();
    }
    public function getCurrentPageSize(): int
    {
        if ($this->currentPage < $this->pagesCount) {
            return $this->limit;
        }
        if ($this->pagesCount === 1 && $this->count > 0) {
            return $this->count;
        }
        if ($this->dataQuery instanceof Countable) {
            $this->paginate();
            return $this->dataQuery->count();
        }
        // ????
        return -1;
    }
    public function getCount(): int
    {
        return $this->count;
    }
    public function withTokenGenerator(?Closure $closure): self
    {
        $paginator = clone $this;
        $paginator->tokenGenerator = $closure;
        return $paginator;
    }
    public function isOnLastPage(): bool
    {
        return $this->currentPage === $this->pagesCount;
    }
    public function isOnFirstPage(): bool
    {
        return $this->currentPage === 1;
    }
    public function getPageToken(int $page): ?string
    {
        if ($page < 1 || $page > $this->pagesCount) {
            return null;
        }
        if (isset($this->pageTokens[$this->limit][$page])) {
            return $this->pageTokens[$this->limit][$page];
        }
        return $this->tokenGenerator === null ? null : ($this->tokenGenerator)($page);
    }
    public function withPreviousPageToken(?string $token): self
    {
        $paginator = clone $this;
        $paginator->pageTokens[$paginator->limit][$paginator->currentPage - 1] = $token;
        return $paginator;
    }
    public function withNextPageToken(?string $token): self
    {
        $paginator = clone $this;
        $paginator->pageTokens[$paginator->limit][$paginator->currentPage + 1] = $token;
        return $paginator;
    }
    public function getPreviousPageToken(): ?string
    {
        return $this->getPageToken($this->currentPage - 1);
    }
    public function getNextPageToken(): ?string
    {
        return $this->getPageToken($this->currentPage + 1);
    }
    public function withPage(int $num): self
    {
        $paginator = clone $this;
        $paginator->currentPage = max(1, min($num, $this->pagesCount));
        return $paginator;
    }
    public function withPageSize(int $limit): self
    {
        $paginator = clone $this;
        $paginator->limit = max($limit, 1);
        $paginator->calcPages();
        return $paginator;
    }
    public function getPageSize(): int
    {
        return $this->limit;
    }
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }
    public function getPagesCount(): int
    {
        return $this->pagesCount;
    }

    private function calcPages(): self
    {
        $this->pagesCount = $this->count > 0
            ? (int)ceil($this->count / $this->limit)
            : $this->pagesCount = 1;
        return $this;
    }
    private function paginate(): self
    {
        $this->dataQuery = clone $this->dataQuery;
        $offset = ($this->currentPage - 1) * $this->limit;
        if ($this->dataQuery instanceof PaginableInterface) {
            $this->dataQuery->limit($this->limit);
            $this->dataQuery->offset($offset);
        }
        return $this;
    }
}
