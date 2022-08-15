<?php

declare(strict_types=1);

namespace App\User;

final class SignupService
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function signup(string $login, string $password): User
    {
        $user = new User(
            UserLogin::createNew($login, $this->userRepository),
            UserPassword::createNew($password)
        );
        $this->userRepository->save($user);
        return $user;
    }
}
