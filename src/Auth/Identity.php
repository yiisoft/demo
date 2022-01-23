<?php

declare(strict_types=1);

namespace App\Auth;

use App\User\User;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use Yiisoft\Security\Random;
use Yiisoft\User\Login\Cookie\CookieLoginIdentityInterface;

/**
 * @Entity(repository="App\Auth\IdentityRepository")
 */
class Identity implements CookieLoginIdentityInterface
{
    /**
     * @Column(type="primary")
     */
    private ?int $id = null;

    /**
     * @Column(type="string(32)")
     */
    private string $authKey;

    /**
     * @BelongsTo(target="App\User\User", nullable=false, load="eager")
     *
     * @var \Cycle\ORM\Promise\Reference|User
     */
    private $user = null;
    private ?int $user_id = null;

    public function __construct()
    {
        $this->regenerateCookieLoginKey();
    }

    public function getId(): ?string
    {
        return $this->user->getId();
    }

    public function getCookieLoginKey(): string
    {
        return $this->authKey;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function validateCookieLoginKey(string $key): bool
    {
        return $this->authKey === $key;
    }

    public function regenerateCookieLoginKey(): void
    {
        $this->authKey = Random::string(32);
    }
}
