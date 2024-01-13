<?php

declare(strict_types=1);

namespace App\Infrastructure\IO\Http\Blog\GetIndex\Response;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *      schema="BlogIndexPost",
 *      @OA\Property(example="100", property="id", format="int"),
 *      @OA\Property(example="Title", property="title", format="string"),
 *      @OA\Property(example="Text", property="content", format="string"),
 * )
 */
final class Response
{
    public function __construct(
        public $id,
        public $title,
        public $content,
    ) {
    }
}
