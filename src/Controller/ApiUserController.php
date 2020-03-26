<?php

namespace App\Controller;

use App\DeferredResponse;
use App\Entity\User;
use App\Repository\UserRepository;
use Cycle\ORM\ORMInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Factory\Factory;

class ApiUserController
{
    public function profile(
        Request $request,
        ORMInterface $orm,
        ContainerInterface $container,
        Factory $factory
    ) {
        /** @var UserRepository $userRepo */
        $userRepo = $orm->getRepository(User::class);
        $login = $request->getAttribute('login', null);

        $item = $userRepo->findByLogin($login);
        if ($item === null) {
            return $factory->create(DeferredResponse::class, [['error' => 'Page not found']])->withStatus(404);
        }

        return  $factory->create(
            DeferredResponse::class,
            [['login' => $item->getLogin(), 'created_at' => $item->getCreatedAt()->format('H:i:s d.m.Y')]]
        );
    }
}
