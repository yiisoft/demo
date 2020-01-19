<?php

namespace App;

use Closure;
use Yiisoft\Data\Paginator\PaginatorInterface;

interface DataPaginatorInterface extends PaginatorInterface
{
    public function withTokenGenerator(Closure $closure): self;
    public function withPage(int $num): self;
    public function getCount(): int;
    public function getCurrentPage(): int;
    public function getPagesCount(): int;
    public function getPageSize(): int;
    public function getPageToken(int $page): ?string;
}
