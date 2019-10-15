<?php

namespace App\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Yiisoft\Security\PasswordHasher;
use Yiisoft\Yii\Web\User\IdentityInterface;

/**
 * @Entity
 */
class User implements IdentityInterface
{
    /**
     * @Column(type="primary")
     * @var int
     */
    private $id;

    /**
     * @Column(type="string")
     * @var string
     */
    private $token;

    /**
     * @Column(type="string")
     * @var string
     */
    private $login;

    /**
     * @Column(type="string")
     * @var string
     */
    private $passwordHash;

    /**
     * @Column(type="string")
     * @var string
     */
    private $name;
    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

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
