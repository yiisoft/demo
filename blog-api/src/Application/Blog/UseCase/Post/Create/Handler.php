<?php

declare(strict_types=1);

namespace App\Application\Blog\UseCase\Post\Create;

use App\Application\Blog\Entity\Post\Post;
use App\Application\Blog\Entity\Post\PostRepository;

final class Handler
{
    public function __construct(
        private PostRepository $postRepository,
    ) {
    }

    public function handle(Command $command)
    {
        $post = new Post();
        $post->setTitle($command->getTitle());
        $post->setContent($command->getContent());
        $post->setStatus($command->getStatus());
        $post->setUser($command->getUser());

        $this->postRepository->save($post);
    }
}
