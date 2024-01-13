<?php

declare(strict_types=1);

namespace App\Infrastructure\IO\Http\User\GetIndex\Response;

use DateTimeImmutable;
use Psr\Http\Message\ResponseInterface;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\DataResponse\DataResponseFactoryInterface;

final class ResponseFactory
{
    public function __construct(
        private DataResponseFactoryInterface $responseFactory,
    ) {
    }

    public function create(DataReaderInterface $dataReader): ResponseInterface
    {
        $response = [];
        foreach ($dataReader->read() as $user) {
            $response[] = new Response(
                $user->getLogin(),
                $user->getCreatedAt()->format(DateTimeImmutable::ATOM)
            );
        }

        return $this->responseFactory->createResponse([
            'users' => $response,
        ]);
    }
}
