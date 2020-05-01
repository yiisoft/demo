<?php

declare(strict_types=1);

namespace App\Blog\Entity;

use App\Entity\User;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use Cycle\Annotated\Annotation\Table;
use Cycle\Annotated\Annotation\Table\Index;
use DateTimeImmutable;

/**
 * @Entity(
 *     repository="App\Blog\Comment\CommentRepository",
 *     mapper="App\Blog\Comment\CommentMapper",
 *     constrain="App\Blog\Comment\Scope\PublicScope"
 * )
 * @Table(
 *     indexes={
 *         @Index(columns={"public","published_at"})
 *     }
 * )
 */
class Comment
{
    /**
     * @Column(type="primary")
     */
    private ?int $id = null;

    /**
     * @Column(type="bool", default="false")
     */
    private bool $public = false;

    /**
     * @Column(type="text")
     */
    private string $content;

    /**
     * @Column(type="datetime")
     */
    private DateTimeImmutable $created_at;

    /**
     * @Column(type="datetime")
     */
    private DateTimeImmutable $updated_at;

    /**
     * @Column(type="datetime", nullable=true)
     */
    private ?DateTimeImmutable $published_at = null;

    /**
     * @Column(type="datetime", nullable=true)
     */
    private ?DateTimeImmutable $deleted_at = null;

    /**
     * @BelongsTo(target="App\Entity\User", nullable=false, load="eager")
     * @var User|\Cycle\ORM\Promise\Reference
     */
    private $user = null;
    private ?int $user_id = null;

    /**
     * @BelongsTo(target="App\Blog\Entity\Post", nullable=false)
     * @var Post|\Cycle\ORM\Promise\Reference
     */
    private $post = null;
    private ?int $post_id = null;

    public function __construct(string $content)
    {
        $this->content = $content;
        $this->created_at = new DateTimeImmutable();
        $this->updated_at = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function isPublic(): bool
    {
        return $this->public;
    }

    public function setPublic(bool $public): void
    {
        $this->public = $public;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function getDeletedAt(): ?DateTimeImmutable
    {
        return $this->deleted_at;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setPost(Post $post): void
    {
        $this->post = $post;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function getPublishedAt(): ?DateTimeImmutable
    {
        return $this->published_at;
    }

    public function setPublishedAt(?DateTimeImmutable $published_at): void
    {
        $this->published_at = $published_at;
    }
}
