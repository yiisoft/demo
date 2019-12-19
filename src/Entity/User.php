<?php

namespace App\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\HasMany;
use Cycle\Annotated\Annotation\Table;
use Cycle\Annotated\Annotation\Table\Index;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Yiisoft\Security\PasswordHasher;
use Yiisoft\Security\Random;
use Yiisoft\Auth\IdentityInterface;

/**
 * @Entity(repository="App\Repository\UserRepository", mapper="Yiisoft\Yii\Cycle\Mapper\TimestampedMapper")
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
    protected $id;

    /**
     * @Column(type="string(128)")
     * @var string
     */
    protected $token;

    /**
     * @Column(type="string(48)")
     * @var string
     */
    protected $login;

    /**
     * @Column(type="string")
     * @var string
     */
    protected $passwordHash;

    /**
     * @var DateTimeImmutable
     */
    protected $createdAt;

    /**
     * @var DateTimeImmutable
     */
    protected $updatedAt;

    /**
     * @HasMany(target="App\Entity\Post")
     * @var ArrayCollection
     */
    protected $posts;

    public function __construct()
    {
        if (!isset($this->token)) {
            $this->resetToken();
        }
        $this->posts = new ArrayCollection();
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
        return (new PasswordHasher())->validate($password, $this->passwordHash);
    }

    public function setPassword(string $password): void
    {
        $this->passwordHash = (new PasswordHasher())->hash($password);
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @return ArrayCollection|Post[]
     */
    public function getPosts(): ArrayCollection
    {
        return $this->posts;
    }

    public function addPost(Post $p): void
    {
        $this->posts->add($p);
    }
}
