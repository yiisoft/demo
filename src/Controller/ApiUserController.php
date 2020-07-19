<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\DataResponse\DataResponseFactoryInterface;

class ApiUserController
{
    private DataResponseFactoryInterface $responseFactory;

    public function __construct(DataResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    public function index(UserRepository $userRepository): ResponseInterface
    {
        $dataReader = $userRepository->findAll()->withSort((new Sort([]))->withOrderString('login'));
        $users = $dataReader->read();

        $items = [];
        foreach ($users as $user) {
            $items[] = ['login' => $user->getLogin(), 'created_at' => $user->getCreatedAt()->format('H:i:s d.m.Y')];
        }

        return $this->responseFactory->createResponse($items);
    }

    public function profile(ServerRequestInterface $request, UserRepository $userRepository): ResponseInterface
    {
        $login = $request->getAttribute('login', null);

        /** @var User $user */
        $user = $userRepository->findByLogin($login);
        if ($user === null) {
            return $this->responseFactory->createResponse('Page not found', 404);
        }

        return $this->responseFactory->createResponse(
            ['login' => $user->getLogin(), 'created_at' => $user->getCreatedAt()->format('H:i:s d.m.Y')]
        );
    }
}
