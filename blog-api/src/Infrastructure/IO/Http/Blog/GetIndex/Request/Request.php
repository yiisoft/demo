<?php

declare(strict_types=1);

namespace App\Infrastructure\IO\Http\Blog\GetIndex\Request;

use OpenApi\Annotations as OA;
use Yiisoft\Input\Http\AbstractInput;
use Yiisoft\Input\Http\Attribute\Parameter\Query;

/**
 * @OA\Parameter(
 *      @OA\Schema(
 *          type="int",
 *          example="2"
 *      ),
 *      in="query",
 *      name="page",
 *      parameter="BlogIndexRequest"
 * )
 */
final class Request extends AbstractInput
{
    #[Query('page')]
    private ?int $page = null;

    private const DEFAULT_PAGE_PARAM = 1;

    public function getPage(): int
    {
        return $this->page ?? self::DEFAULT_PAGE_PARAM;
    }
}
