<?php
namespace App\Entity;

use Yiisoft\Security\PasswordHasher;
use Yiisoft\Yii\Web\User\IdentityInterface;

class User implements IdentityInterface
{
    private $id;
    private $token;
    private $login;
    private $passwordHash;

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
        return (new PasswordHasher())->validate($password, $this->passwordHash);
    }

    public function setPassword(string $password): void
    {
        $this->passwordHash = (new PasswordHasher())->hash($password);
    }
}
