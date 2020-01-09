<?php

namespace App\Repository;

use App\Entity\User;
use Yiisoft\Auth\IdentityInterface;
use Yiisoft\Auth\IdentityRepositoryInterface;

class UserRepository implements IdentityRepositoryInterface
{
    private const IDENTITIES = [
        [
            'id' => '1',
            'token' => 'test1',
            'login' => 'samdark',
            'password' => 'qwerty',
        ],
        [
            'id' => '2',
            'token' => 'test2',
            'login' => 'hiqsol',
            'password' => 'qwerty',
        ],
    ];

    private function findIdentityBy(string $field, string $value): ?IdentityInterface
    {
        foreach (self::IDENTITIES as $identity) {
            if ($identity[$field] === $value) {
                $user = new User($identity['id'], $identity['login']);
                $user->setToken($identity['token']);
                $user->setPassword($identity['password']);
                return $user;
            }
        }

        return null;
    }

    public function findIdentity(string $id): ?IdentityInterface
    {
        return $this->findIdentityBy('id', $id);
    }

    public function findIdentityByToken(string $token, string $type): ?IdentityInterface
    {
        return $this->findIdentityBy('token', $token);
    }

    public function findByLogin(string $login): ?User
    {
        return $this->findIdentityBy('login', $login);
    }
}
