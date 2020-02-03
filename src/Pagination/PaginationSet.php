<?php

namespace App\Pagination;

use Closure;
use Yiisoft\Data\Paginator\OffsetPaginatorInterface;

class PaginationSet
{
    private OffsetPaginatorInterface $paginator;
    private Closure $pageUrlGenerator;

    public function __construct(
        OffsetPaginatorInterface $paginator,
        Closure $pageUrlGenerator
    ) {
        $this->paginator = $paginator;
        $this->pageUrlGenerator = $pageUrlGenerator;
    }

    public function getPageUrl(int $page): string
    {
        return ($this->pageUrlGenerator)($page);
    }

    public function getPaginator(): OffsetPaginatorInterface
    {
        return $this->paginator;
    }

    public function needToPaginate(): bool
    {
        return $this->getPaginator()->isRequired();
    }
}
