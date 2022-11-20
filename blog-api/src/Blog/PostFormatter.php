<?php

declare(strict_types=1);

namespace App\Blog;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *      schema="Post",
 *      @OA\Property(example="100", property="id", format="int"),
 *      @OA\Property(example="Title", property="title", format="string"),
 *      @OA\Property(example="Text", property="content", format="string"),
 * )
 */
final class PostFormatter
{
    public function format(Post $post): array
    {
        return [
            'id' => $post->getId(),
            'title' => $post->getTitle(),
            'content' => $post->getContent(),
        ];
    }
}
