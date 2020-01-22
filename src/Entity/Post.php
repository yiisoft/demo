<?php

namespace App\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use Cycle\Annotated\Annotation\Relation\HasMany;
use Cycle\Annotated\Annotation\Relation\ManyToMany;
use Cycle\Annotated\Annotation\Table;
use Cycle\Annotated\Annotation\Table\Index;
use Cycle\ORM\Relation\Pivoted\PivotedCollection;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Yiisoft\Security\Random;

/**
 * @Entity(
 *     repository="App\Repository\PostRepository",
 *     mapper="App\Mapper\PostMapper",
 *     constrain="App\Constrain\PostPublic"
 * )
 * @Table(
 *     indexes={
 *         @Index(columns={"public","publishedAt"}),
 *     }
 * )
 */
class Post
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
    private $slug;

    /**
     * @Column(type="string(255)")
     * @var string
     */
    private $title;

    /**
     * @Column(type="bool", default="false")
     * @var bool
     */
    private $public;

    /**
     * @Column(type="text")
     * @var string
     */
    private $content;

    /**
     * @Column(type="datetime")
     * @var DateTimeImmutable
     */
    private $createdAt;

    /**
     * @Column(type="datetime")
     * @var DateTimeImmutable
     */
    private $updatedAt;

    /**
     * @Column(type="datetime", nullable=true)
     * @var DateTimeImmutable|null
     */
    private $publishedAt;

    /**
     * @Column(type="datetime", nullable=true)
     * @var DateTimeImmutable|null
     */
    private $deletedAt;

    /**
     * @BelongsTo(target="App\Entity\User", nullable=false)
     * @var User|\Cycle\ORM\Promise\Reference
     */
    private $user;

    /**
     * @ManyToMany(target="App\Entity\Tag", though="PostTag", fkAction="CASCADE")
     * @var PivotedCollection
     */
    private $tags;

    /**
     * @HasMany(target="App\Entity\Comment")
     * @var ArrayCollection
     */
    private $comments;

    public function __construct()
    {
        $this->tags = new PivotedCollection();
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
