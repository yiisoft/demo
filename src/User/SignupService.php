<?php

declare(strict_types=1);

namespace App\User;

final class SignupService
{
    private UserLogin $login;
    private UserPassword $password;

    public function __construct(private UserRepository $userRepository)
    {
    }

    public function signup(): User
    {
        $user = new User($this->login, $this->password);
        $this->userRepository->save($user);
        return $user;
    }

    public function setLogin(string|UserLogin $login): SignupService
    {
        if (is_string($login)) {
            $this->login = UserLogin::createNew($login, $this->userRepository);
        }else {
            $this->login = $login;
        }

        return $this;
    }

    public function setPassword(string|UserPassword $password): SignupService
    {
        if (is_string($password)) {
            $this->password = UserPassword::createNew($password,);
        }else {
            $this->password = $password;
        }

        return $this;
    }
}
