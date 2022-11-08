<?php

declare(strict_types=1);

namespace App\Blog;

final class PostBuilder
{
    public function build(Post $post, EditPostRequest $request): Post
    {
        $post->setTitle($request->getTitle());
        $post->setContent($request->getText());
        $post->setStatus($request->getStatus());

        return $post;
    }
}
