<?php

declare(strict_types=1);

namespace App\User;

use Yiisoft\Access\AccessCheckerInterface;
use Yiisoft\User\User as UserComponent;

final class UserService
{
    private UserComponent $user;
    private UserRepository $repository;
    private AccessCheckerInterface $accessChecker;

    public function __construct(UserComponent $user, UserRepository $repository, AccessCheckerInterface $accessChecker)
    {
        $this->user = $user;
        $this->repository = $repository;
        $this->accessChecker = $accessChecker;
    }

    public function getUser(): ?User
    {
        return $this->repository->findIdentity($this->user->getId());
    }

    public function hasPermission(string $permission): bool
    {
        $userId = $this->user->getId();
        return null !== $userId && $this->accessChecker->userHasPermission($userId, $permission);
    }
}
