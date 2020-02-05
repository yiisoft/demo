<?php

namespace App\Pagination;

use Closure;
use Yiisoft\Data\Paginator\OffsetPaginator;

/**
 * @deprecated Should be replaced to a Pagination Widget
 */
class PaginationSet
{
    private OffsetPaginator $paginator;
    private Closure $pageUrlGenerator;

    public function __construct(
        OffsetPaginator $paginator,
        Closure $pageUrlGenerator
    ) {
        $this->paginator = $paginator;
        $this->pageUrlGenerator = $pageUrlGenerator;
    }

    public function getPageUrl(int $page): string
    {
        return ($this->pageUrlGenerator)($page);
    }

    public function getPaginator(): OffsetPaginator
    {
        return $this->paginator;
    }

    public function needToPaginate(): bool
    {
        return $this->getPaginator()->isRequired();
    }
}
