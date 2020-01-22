<?php

namespace App\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Table;
use Cycle\Annotated\Annotation\Table\Index;
use Yiisoft\Security\PasswordHasher;
use Yiisoft\Security\Random;
use Yiisoft\Auth\IdentityInterface;

/**
 * @Entity(repository="App\Repository\UserRepository")
 * @Table(
 *     indexes={
 *         @Index(columns={"login"}, unique=true),
 *         @Index(columns={"token"}, unique=true)
 *     }
 * )
 */
class User implements IdentityInterface
{
    /**
     * @Column(type="primary")
     * @var int
     */
    private $id;

    /**
     * @Column(type="string(128)")
     * @var string
     */
    private $token;

    /**
     * @Column(type="string(48)")
     * @var string
     */
    private $login;

    /**
     * @Column(type="string")
     * @var string
     */
    private $passwordHash;

    public function __construct()
    {
        if (!isset($this->token)) {
            $this->resetToken();
        }
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function resetToken(): void
    {
        $this->token = Random::string(128);
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function setLogin(string $login): void
    {
        $this->login = $login;
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
