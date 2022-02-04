<?php

declare(strict_types=1);

namespace App\User;

use App\Auth\Identity;
use App\Blog\Entity\Comment;
use App\Blog\Entity\Post;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\HasMany;
use Cycle\Annotated\Annotation\Relation\HasOne;
use Cycle\Annotated\Annotation\Table\Index;
use Cycle\ORM\Entity\Behavior;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Yiisoft\Security\PasswordHasher;

#[Entity(repository: \App\User\UserRepository::class)]
#[Index(columns: ['login'], unique: true)]
#[Behavior\CreatedAt(field: 'created_at', column: 'created_at')]
#[Behavior\UpdatedAt(field: 'updated_at', column: 'updated_at')]
class User
{
    #[Column(type: 'primary')]
    private ?int $id = null;

    #[Column(type: 'string(48)')]
    private string $login;

    #[Column(type: 'string')]
    private string $passwordHash;

    #[Column(type: 'datetime')]
    private DateTimeImmutable $created_at;

    #[Column(type: 'datetime')]
    private DateTimeImmutable $updated_at;

    #[HasOne(target: Identity::class)]
    private Identity $identity;

    /**
     * @var ArrayCollection<array-key, Post>
     */
    #[HasMany(target: \App\Blog\Entity\Post::class)]
    private ArrayCollection $posts;

    /**
     * @var ArrayCollection<array-key, Comment>
     */
    #[HasMany(target: \App\Blog\Entity\Comment::class)]
    private ArrayCollection $comments;

    public function __construct(string $login, string $password)
    {
        $this->login = $login;
        $this->created_at = new DateTimeImmutable();
        $this->updated_at = new DateTimeImmutable();
        $this->setPassword($password);
        $this->identity = new Identity();
        $this->posts = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id === null ? null : (string)$this->id;
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
        return $this->created_at;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function getIdentity(): Identity
    {
        return $this->identity;
    }

    /**
     * @return Post[]
     */
    public function getPosts(): array
    {
        return $this->posts->toArray();
    }

    public function addPost(Post $post): void
    {
        $this->posts->add($post);
    }

    /**
     * @return Comment[]
     */
    public function getComments(): array
    {
        return $this->comments->toArray();
    }

    public function addComment(Comment $post): void
    {
        $this->comments->add($post);
    }
}
