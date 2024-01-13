<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yiisoft\DataResponse\DataResponseFactoryInterface;
use Yiisoft\Http\Status;

final class AuthRequestErrorHandler implements RequestHandlerInterface
{
    public function __construct(private DataResponseFactoryInterface $dataResponseFactory)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->dataResponseFactory->createResponse('Unauthorised request', Status::UNAUTHORIZED);
    }
}
