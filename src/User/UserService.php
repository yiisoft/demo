<?php

declare(strict_types=1);

namespace App\User;

use Yiisoft\Access\AccessCheckerInterface;
use Yiisoft\User\CurrentUser;

final class UserService
{
    private CurrentUser $currentUser;
    private UserRepository $repository;
    private AccessCheckerInterface $accessChecker;

    public function __construct(CurrentUser $currentUser, UserRepository $repository, AccessCheckerInterface $accessChecker)
    {
        $this->currentUser = $currentUser;
        $this->repository = $repository;
        $this->accessChecker = $accessChecker;
    }

    public function getUser(): ?User
    {
        $userId = $this->currentUser->getId();

        if ($userId === null) {
            return null;
        }

        return $this->repository->findById($this->currentUser->getId());
    }

    public function hasPermission(string $permission): bool
    {
        $userId = $this->currentUser->getId();
        return null !== $userId && $this->accessChecker->userHasPermission($userId, $permission);
    }
}
