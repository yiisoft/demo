<?php

declare(strict_types=1);

namespace App\Auth;

use App\User\UserRepository;
use Throwable;
use Yiisoft\Auth\IdentityInterface;
use Yiisoft\User\CurrentUser;

final class AuthService
{
    public function __construct(
        private CurrentUser $currentUser,
        private UserRepository $userRepository,
        private IdentityRepository $identityRepository,
    ) {
    }

    public function login(string $login, string $password): bool
    {
        $user = $this->userRepository->findByLoginWithAuthIdentity($login);

        if ($user === null || !$user->validatePassword($password)) {
            return false;
        }

        return $this->currentUser->login($user->getIdentity());
    }

    /**
     * @throws Throwable
     */
    public function logout(): bool
    {
        $identity = $this->currentUser->getIdentity();

        if ($identity instanceof Identity) {
            $identity->regenerateCookieLoginKey();
            $this->identityRepository->save($identity);
        }

        return $this->currentUser->logout();
    }

    public function getIdentity(): IdentityInterface
    {
        return $this->currentUser->getIdentity();
    }

    public function isGuest(): bool
    {
        return $this->currentUser->isGuest();
    }
}
