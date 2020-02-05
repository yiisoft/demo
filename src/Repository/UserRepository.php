<?php

namespace App\Repository;

use Cycle\ORM\Select;
use Yiisoft\Auth\IdentityInterface;
use Yiisoft\Auth\IdentityRepositoryInterface;
use Yiisoft\Yii\Cycle\DataReader\SelectDataReader;

class UserRepository extends Select\Repository implements IdentityRepositoryInterface
{
    public function findAll(array $scope = [], array $orderBy = []): SelectDataReader
    {
        return new SelectDataReader($this->select()->where($scope)->orderBy($orderBy));
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
