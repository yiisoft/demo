<?php

namespace App\Controller;

use App\DeferredResponseFactory;
use App\Entity\User;
use App\Repository\UserRepository;
use Cycle\ORM\ORMInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Web\ResponseFactory;

class ApiUserController
{
    private ResponseFactory $responseFactory;

    public function __construct(ResponseFactory $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    public function index(ORMInterface $orm): ResponseInterface
    {
        /** @var UserRepository $userRepo */
        $userRepo = $orm->getRepository(User::class);

        $dataReader = $userRepo->findAll()->withSort((new Sort([]))->withOrderString('login'));
        $users = $dataReader->read();

        $items = [];
        foreach ($users as $user) {
            $items[] = ['login' => $user->getLogin(), 'created_at' => $user->getCreatedAt()->format('H:i:s d.m.Y')];
        }

        return $this->responseFactory->createResponse(200, '', $items);
    }

    public function profile(Request $request, ORMInterface $orm): ResponseInterface
    {
        /** @var UserRepository $userRepository */
        $userRepository = $orm->getRepository(User::class);
        $login = $request->getAttribute('login', null);

        /** @var User $user */
        $user = $userRepository->findByLogin($login);
        if ($user === null) {
            return $this->responseFactory->createResponse(404, '', 'Page not found');
        }

        return $this->responseFactory->createResponse(
            200,
            '',
            ['login' => $user->getLogin(), 'created_at' => $user->getCreatedAt()->format('H:i:s d.m.Y')]
        );
    }
}
