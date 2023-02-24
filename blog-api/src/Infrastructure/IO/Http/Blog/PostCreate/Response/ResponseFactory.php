<?php

declare(strict_types=1);

namespace App\Infrastructure\IO\Http\Blog\PostCreate\Response;

use Psr\Http\Message\ResponseInterface;
use Yiisoft\DataResponse\DataResponseFactoryInterface;

final class ResponseFactory
{
    public function __construct(
        private DataResponseFactoryInterface $responseFactory,
    ) {
    }

    public function create(): ResponseInterface
    {
        return $this->responseFactory->createResponse();
    }
}
