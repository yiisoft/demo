<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Yiisoft\Access\AccessCheckerInterface;
use Yiisoft\Yii\Web\User\User as UserComponent;

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
        return !is_null($userId) && $this->accessChecker->userHasPermission($userId, $permission);
    }
}
