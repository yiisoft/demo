<?php

declare(strict_types=1);

namespace App\Infrastructure\IO\Http\User\GetIndex\Response;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *      schema="UserIndexResponse",
 *      @OA\Property(example="UserName", property="login", format="string"),
 *      @OA\Property(example="13.12.2020 00:04:20", property="created_at", format="string"),
 * )
 */
final class Response
{
    public function __construct(
        public string $login,
        public string $created_at,
    ) {
    }
}
