<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Yiisoft\Access\AccessCheckerInterface;
use Yiisoft\Auth\IdentityInterface;

final class UserService
{
    private UserRepository $repository;
    private AccessCheckerInterface $accessChecker;

    public function __construct(UserRepository $repository, AccessCheckerInterface $accessChecker)
    {
        $this->repository = $repository;
        $this->accessChecker = $accessChecker;
    }

    public function getUser(IdentityInterface $identity): ?User
    {
        return $this->repository->findIdentity($identity->getId());
    }

    public function hasPermission(string $permission, IdentityInterface $identity): bool
    {
        $userId = $identity->getId();
        return !is_null($userId) && $this->accessChecker->userHasPermission($userId, $permission);
    }
}
