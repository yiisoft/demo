<?php

declare(strict_types=1);

namespace App\Infrastructure\IO\Http\Blog\GetIndex\Response;

use App\Infrastructure\Http\Formatter\PaginatorFormatter;
use Psr\Http\Message\ResponseInterface;
use Yiisoft\Data\Paginator\PaginatorInterface;
use Yiisoft\DataResponse\DataResponseFactoryInterface;

final class ResponseFactory
{
    public function __construct(
        private DataResponseFactoryInterface $responseFactory,
        private PaginatorFormatter $paginatorFormatter,
    ) {
    }

    public function create(PaginatorInterface $paginator): ResponseInterface
    {
        $response = [];
        foreach ($paginator->read() as $post) {
            $response[] = new Response(
                $post->getId(),
                $post->getTitle(),
                $post->getContent(),
            );
        }

        return $this->responseFactory->createResponse([
            'posts' => $response,
            'paginator' => $this->paginatorFormatter->format($paginator),
        ]);
    }
}
