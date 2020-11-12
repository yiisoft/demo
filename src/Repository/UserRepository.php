<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Cycle\ORM\Select;
use Yiisoft\Auth\IdentityInterface;
use Yiisoft\Auth\IdentityRepositoryInterface;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Yii\Cycle\DataReader\SelectDataReader;

class UserRepository extends Select\Repository implements IdentityRepositoryInterface
{
    public function findAll(array $scope = [], array $orderBy = []): DataReaderInterface
    {
        return new SelectDataReader($this->select()->where($scope)->orderBy($orderBy));
    }

    private function findIdentityBy(string $field, string $value): ?IdentityInterface
    {
        return $this->findOne([$field => $value]);
    }

    /**
     * @param string $id
     * @return IdentityInterface|User|null
     */
    public function findIdentity(string $id): ?IdentityInterface
    {
        return $this->findByPK($id);
    }

    public function findIdentityByToken(string $token, string $type = null): ?IdentityInterface
    {
        return $this->findIdentityBy('token', $token);
    }

    public function findByLogin(string $login): ?IdentityInterface
    {
        return $this->findIdentityBy('login', $login);
    }
}
