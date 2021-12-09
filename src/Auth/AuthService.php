<?php

declare(strict_types=1);

namespace App\Auth;

use App\User\User;
use App\User\UserRepository;
use InvalidArgumentException;
use Throwable;
use Yiisoft\User\CurrentUser;

final class AuthService
{
    private CurrentUser $currentUser;
    private UserRepository $userRepository;
    private IdentityRepository $identityRepository;

    public function __construct(
        CurrentUser $currentUser,
        UserRepository $userRepository,
        IdentityRepository $identityRepository,
    ) {
        $this->currentUser = $currentUser;
        $this->userRepository = $userRepository;
        $this->identityRepository = $identityRepository;
    }

    public function login(string $login, string $password, bool $rememberMe = false): bool
    {
        $user = $this->userRepository->findByLoginWithAuthIdentity($login);

        if ($user === null || !$user->validatePassword($password)) {
            return false;
        }

        $identity = $user->getIdentity();
        $identity->setShouldLoginByCookie($rememberMe);

        return $this->currentUser->login($identity);
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

    /**
     * @throws Throwable
     */
    public function signup(string $login, string $password): bool
    {
        $user = $this->userRepository->findByLogin($login);

        if ($user !== null) {
            return false;
        }

        $user = new User($login, $password);
        $this->userRepository->save($user);

        return true;
    }

    public function isGuest(): bool
    {
        return $this->currentUser->isGuest();
    }
}
