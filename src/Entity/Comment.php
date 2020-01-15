<?php

namespace App\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use Cycle\Annotated\Annotation\Table;
use Cycle\Annotated\Annotation\Table\Index;
use DateTimeImmutable;

/**
 * @Entity(
 *     mapper="App\Mapper\CommentMapper",
 *     constrain="App\Constrain\CommentDefault"
 * )
 * @Table(
 *     indexes={
 *         @Index(columns={"public"})
 *     }
 * )
 */
class Comment
{
    /**
     * @Column(type="primary")
     * @var int
     */
    protected $id;

    /**
     * @Column(type="bool", default="false")
     * @var bool
     */
    protected $public;

    /**
     * @Column(type="text")
     * @var string
     */
    protected $content;

    /**
     * @Column(type="datetime")
     * @var DateTimeImmutable
     */
    protected $createdAt;

    /**
     * @Column(type="datetime")
     * @var DateTimeImmutable
     */
    protected $updatedAt;

    /**
     * @Column(type="datetime", nullable=true)
     * @var DateTimeImmutable|null
     */
    protected $publishedAt;

    /**
     * @Column(type="datetime", nullable=true)
     * @var DateTimeImmutable|null
     */
    protected $deletedAt;

    /**
     * @BelongsTo(target="App\Entity\User", nullable=false, load="eager")
     * @var User|\Cycle\ORM\Promise\Reference
     */
    protected $user;

    /**
     * @BelongsTo(target="App\Entity\Post", nullable=false)
     * @var Post|\Cycle\ORM\Promise\Reference
     */
    protected $post;

    public function getId(): ?string
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
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getDeletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setPost(Post $post)
    {
        $this->post = $post;
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    public function getPublishedAt(): ?DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?DateTimeImmutable $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }
}
