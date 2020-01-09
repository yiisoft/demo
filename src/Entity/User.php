<?php
namespace App\Entity;

use Yiisoft\Security\PasswordHasher;
use Yiisoft\Auth\IdentityInterface;

class User implements IdentityInterface
{
    private string $id;
    private ?string $token = null;
    private string $login;
    private ?string $passwordHash = null;

    public function __construct(string $id, string $login)
    {
        $this->id = $id;
        $this->login = $login;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function validatePassword(string $password): bool
    {
        if ($this->passwordHash === null) {
            return false;
        }
        return (new PasswordHasher())->validate($password, $this->passwordHash);
    }

    public function setPassword(string $password): void
    {
        $this->passwordHash = (new PasswordHasher())->hash($password);
    }
}
