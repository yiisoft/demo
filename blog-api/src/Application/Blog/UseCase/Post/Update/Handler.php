<?php

declare(strict_types=1);

namespace App\Application\Blog\UseCase\Post\Update;

use App\Application\Blog\Entity\Post\PostRepository;
use App\Application\Blog\Service\BlogService;

final class Handler
{
    public function __construct(
        private PostRepository $postRepository,
        private BlogService $blogService,
    ) {
    }

    public function handle(Command $command)
    {
        $post = $this->blogService->getPost($command->getId());
        $post->setTitle($command->getTitle());
        $post->setContent($command->getContent());
        $post->setStatus($command->getStatus());

        $this->postRepository->save($post);
    }
}
