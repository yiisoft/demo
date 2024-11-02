<?php

declare(strict_types=1);

namespace App\Formatter;

use OpenApi\Attributes as OA;
use Yiisoft\Data\Paginator\OffsetPaginator;

#[OA\Schema(
    schema: 'Paginator',
    properties: [
        new OA\Property(property: 'pageSize', type: 'int', example: '10'),
        new OA\Property(property: 'currentPage', type: 'int', example: '1'),
        new OA\Property(property: 'totalPages', type: 'int', example: '3'),
    ]
)]
final class PaginatorFormatter
{
    public function format(OffsetPaginator $paginator): array
    {
        return [
            'pageSize' => $paginator->getPageSize(),
            'currentPage' => $paginator->getCurrentPage(),
            'totalPages' => $paginator->getTotalPages(),
        ];
    }
}
