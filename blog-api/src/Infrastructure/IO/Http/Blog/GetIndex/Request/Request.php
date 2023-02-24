<?php

declare(strict_types=1);

namespace App\Infrastructure\IO\Http\Blog\GetIndex\Request;

use OpenApi\Annotations as OA;
use Yiisoft\RequestModel\RequestModel;

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
final class Request extends RequestModel
{
    private const DEFAULT_PAGE_PARAM = 1;

    public function getPage(): int
    {
        if ($this->hasAttribute('query.page')) {
            return (int)$this->getAttributeValue('query.page');
        }

        return self::DEFAULT_PAGE_PARAM;
    }
}
