<?php

namespace App\Controller;

use App\DeferredResponse;
use App\Entity\User;
use App\Repository\UserRepository;
use Cycle\ORM\ORMInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Factory\Factory;
use Psr\Http\Message\ResponseInterface;

class ApiUserController
{
    public function index(Request $request, ORMInterface $orm, Factory $factory): ResponseInterface
    {
        /** @var UserRepository $userRepo */
        $userRepo = $orm->getRepository(User::class);

        $dataReader = $userRepo->findAll()->withSort((new Sort([]))->withOrderString('login'));
        $users = $dataReader->read();

        $items = [];
        foreach ($users as $user) {
            $items[] = ['login' => $user->getLogin(), 'created_at' => $user->getCreatedAt()->format('H:i:s d.m.Y')];
        }

        return  $factory->create(DeferredResponse::class, [$items]);
    }

    public function profile(Request $request, ORMInterface $orm, Factory $factory): ResponseInterface
    {
        /** @var UserRepository $userRepo */
        $userRepo = $orm->getRepository(User::class);
        $login = $request->getAttribute('login', null);

        $item = $userRepo->findByLogin($login);
        if ($item === null) {
            return $factory->create(DeferredResponse::class, [['error' => 'Page not found']])->withStatus(404);
        }

        return $factory->create(
            DeferredResponse::class,
            [['login' => $item->getLogin(), 'created_at' => $item->getCreatedAt()->format('H:i:s d.m.Y')]]
        );
    }
}
