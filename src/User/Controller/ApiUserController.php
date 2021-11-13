<?php

declare(strict_types=1);

namespace App\User\Controller;

use App\User\User;
use App\User\UserRepository;
use Psr\Http\Message\ResponseInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\DataResponse\DataResponseFactoryInterface;
use Yiisoft\Router\CurrentRouteInterface;

class ApiUserController
{
    private DataResponseFactoryInterface $responseFactory;

    public function __construct(DataResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    public function index(UserRepository $userRepository): ResponseInterface
    {
        $dataReader = $userRepository->findAll()->withSort(Sort::only(['login'])->withOrderString('login'));
        $users = $dataReader->read();

        $items = [];
        foreach ($users as $user) {
            $items[] = ['login' => $user->getLogin(), 'created_at' => $user->getCreatedAt()->format('H:i:s d.m.Y')];
        }

        return $this->responseFactory->createResponse($items);
    }

    public function profile(UserRepository $userRepository, CurrentRouteInterface $currentRoute): ResponseInterface
    {
        $login = $currentRoute->getParameter('login');

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
