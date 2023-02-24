<?php

declare(strict_types=1);

namespace App\Infrastructure\IO\Http\Blog\GetView\Response;

use App\Application\Blog\Entity\Post\Post;
use Psr\Http\Message\ResponseInterface;
use Yiisoft\DataResponse\DataResponseFactoryInterface;

final class ResponseFactory
{
    public function __construct(
        private DataResponseFactoryInterface $responseFactory,
    ) {
    }

    public function create(Post $post): ResponseInterface
    {
        $response = new Response(
            $post->getId(),
            $post->getTitle(),
            $post->getContent(),
        );

        return $this->responseFactory->createResponse([
            'post' => $response,
        ]);
    }
}
