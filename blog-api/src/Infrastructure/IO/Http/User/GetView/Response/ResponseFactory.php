<?php

declare(strict_types=1);

namespace App\Infrastructure\IO\Http\User\GetView\Response;

use App\Application\User\Entity\User;
use DateTimeImmutable;
use Psr\Http\Message\ResponseInterface;
use Yiisoft\DataResponse\DataResponseFactoryInterface;

final class ResponseFactory
{
    public function __construct(
        private DataResponseFactoryInterface $responseFactory,
    ) {
    }

    public function create(User $user): ResponseInterface
    {
        $response = new Response(
            $user->getLogin(),
            $user->getCreatedAt()->format(DateTimeImmutable::ATOM)
        );

        return $this->responseFactory->createResponse([
            'user' => $response,
        ]);
    }
}
