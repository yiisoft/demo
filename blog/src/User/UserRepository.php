<?php

declare(strict_types=1);

namespace App\User;

use Cycle\ORM\Select;
use Throwable;
use Yiisoft\Data\Cycle\Reader\EntityReader;
use Yiisoft\Data\Cycle\Writer\EntityWriter;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;

final class UserRepository extends Select\Repository
{
    public function __construct(private EntityWriter $entityWriter, Select $select)
    {
        parent::__construct($select);
    }

    /**
     * @psalm-return DataReaderInterface<int, User>
     */
    public function getReader(): DataReaderInterface
    {
        return (new EntityReader($this->select()))->withSort($this->getSort());
    }

    private function getSort(): Sort
    {
        return Sort::only(['id', 'login'])->withOrder(['id' => 'asc']);
    }

    public function findAll(array $scope = [], array $orderBy = []): DataReaderInterface
    {
        return new EntityReader($this
            ->select()
            ->where($scope)
            ->orderBy($orderBy));
    }

    /**
     * @param string $id
     *
     * @return User|null
     */
    public function findById(string $id): ?User
    {
        return $this->findByPK($id);
    }

    public function findByLogin(string $login): ?User
    {
        return $this->findBy('login', $login);
    }

    public function findByLoginWithAuthIdentity(string $login): ?User
    {
        return $this
            ->select()
            ->where(['login' => $login])
            ->load('identity')
            ->fetchOne();
    }

    /**
     * @throws Throwable
     */
    public function save(User $user): void
    {
        $this->entityWriter->write([$user]);
    }

    private function findBy(string $field, string $value): ?User
    {
        return $this->findOne([$field => $value]);
    }
}
