<?php

namespace App\Repository;

use App\Entity\User;
use Cycle\ORM\ORMInterface;
use Yiisoft\Yii\Web\User\IdentityInterface;
use Yiisoft\Yii\Web\User\IdentityRepositoryInterface;

class UserRepository implements IdentityRepositoryInterface
{
    private $orm;

    public function __construct(ORMInterface $orm)
    {
        $this->orm = $orm;
    }

    private function findIdentityBy(string $field, string $value): ?IdentityInterface
    {
        return $this->orm->getRepository(User::class)->findOne([$field => $value]);
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
