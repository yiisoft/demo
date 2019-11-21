<?php

namespace App\Repository;

use App\Entity\User;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Select;
use Yiisoft\Auth\IdentityInterface;
use Yiisoft\Auth\IdentityRepositoryInterface;

class UserRepository extends Select\Repository implements IdentityRepositoryInterface
{
    public function __construct(ORMInterface $orm, $role = User::class)
    {
        parent::__construct(new Select($orm, $role));
    }

    private function findIdentityBy(string $field, string $value): ?IdentityInterface
    {
        return $this->findOne([$field => $value]);
    }

    public function findIdentity(string $id): ?IdentityInterface
    {
        return $this->findByPK($id);
    }

    public function findIdentityByToken(string $token, string $type): ?IdentityInterface
    {
        return $this->findIdentityBy('token', $token);
    }

    public function findByLogin(string $login): ?IdentityInterface
    {
        return $this->findIdentityBy('login', $login);
    }
}
