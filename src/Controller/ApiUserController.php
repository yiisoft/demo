<?php

namespace App\Controller;

use App\DeferredResponse;
use App\Entity\User;
use App\Repository\UserRepository;
use Cycle\ORM\ORMInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\StreamFactoryInterface;

class ApiUserController
{
    public function profile(
        Request $request,
        ORMInterface $orm,
        ContainerInterface $container,
        ResponseFactoryInterface $responseFactory,
        StreamFactoryInterface $streamFactory
    ) {
        /** @var UserRepository $userRepo */
        $userRepo = $orm->getRepository(User::class);
        $login = $request->getAttribute('login', null);

        $item = $userRepo->findByLogin($login);
        if ($item === null) {
            return $responseFactory->createResponse(404);
        }

        return new DeferredResponse(
            ['login' => $item->getLogin(), 'created_at' => $item->getCreatedAt()->format('H:i:s d.m.Y')],
            $responseFactory,
            $streamFactory
        );
    }
}
