<?php

declare(strict_types=1);

namespace App\Blog;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Post',
    properties: [
        new OA\Property(property: 'id', type: 'int', example: '100'),
        new OA\Property(property: 'title', type: 'string', example: 'Title'),
        new OA\Property(property: 'content', type: 'string', example: 'Text'),
    ]
)]
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
