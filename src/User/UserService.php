<?php

declare(strict_types=1);

namespace App\User;

use Yiisoft\Access\AccessCheckerInterface;
use Yiisoft\User\CurrentIdentity\CurrentIdentity;

final class UserService
{
    private CurrentIdentity $currentIdentity;
    private UserRepository $repository;
    private AccessCheckerInterface $accessChecker;

    public function __construct(CurrentIdentity $currentIdentity, UserRepository $repository, AccessCheckerInterface $accessChecker)
    {
        $this->currentIdentity = $currentIdentity;
        $this->repository = $repository;
        $this->accessChecker = $accessChecker;
    }

    public function getUser(): ?User
    {
        return $this->repository->findIdentity($this->currentIdentity->getId());
    }

    public function hasPermission(string $permission): bool
    {
        $userId = $this->currentIdentity->getId();
        return null !== $userId && $this->accessChecker->userHasPermission($userId, $permission);
    }
}
