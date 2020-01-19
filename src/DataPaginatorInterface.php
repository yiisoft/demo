<?php

namespace App;

use Closure;
use Yiisoft\Data\Paginator\PaginatorInterface;

interface DataPaginatorInterface extends PaginatorInterface
{
    public function getCurrentPage(): int;
    public function getItemsCount(): int;
    public function getOffset(): int;
    public function getPageSize(): int;
    public function getPageToken(int $page): ?string;
    public function getTotalPages(): int;
    public function withCurrentPage(int $num): self;
    public function withTokenGenerator(Closure $closure): self;
}
