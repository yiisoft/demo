<?php

namespace App\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use Cycle\Annotated\Annotation\Relation\HasMany;
use Cycle\Annotated\Annotation\Relation\ManyToMany;
use Cycle\Annotated\Annotation\Table;
use Cycle\Annotated\Annotation\Table\Index;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Yiisoft\Security\Random;

/**
 * @Entity(
 *     repository="App\Repository\PostRepository",
 *     mapper="App\Mapper\PostMapper",
 *     constrain="App\Constrain\PostDefault"
 * )
 * @Table(
 *     indexes={
 *         @Index(columns={"publishedAt"}),
 *         @Index(columns={"public"})
 *     }
 * )
 */
class Post
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
    protected $slug;

    /**
     * @Column(type="string(255)")
     * @var string
     */
    protected $title;

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
     * @BelongsTo(target="App\Entity\User", nullable=false)
     * @var User|\Cycle\ORM\Promise\Reference
     */
    protected $user;

    /**
     * @ManyToMany(target="App\Entity\Tag", though="PostTag", fkAction="CASCADE")
     * @var ArrayCollection
     */
    protected $tags;

    /**
     * @HasMany(target="App\Entity\Comment")
     * @var ArrayCollection
     */
    protected $comments;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->comments = new ArrayCollection();
        if (!isset($this->slug)) {
            $this->resetSlug();
        }
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function resetSlug(): void
    {
        $this->slug = Random::string(128);
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
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

    public function getPublishedAt(): ?DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?DateTimeImmutable $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }

    /**
     * @return ArrayCollection|Comment[]
     */
    public function getComments()
    {
        return $this->comments;
    }

    public function addComment(Comment $post): void
    {
        $this->comments->add($post);
    }

    /**
     * @return ArrayCollection|Tag[]
     */
    public function getTags()
    {
        return $this->tags;
    }

    public function addTag(Tag $post): void
    {
        $this->tags->add($post);
    }
}
