<?php

declare(strict_types=1);

namespace App\Blog\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\ManyToMany;
use Cycle\Annotated\Annotation\Table\Index;
use Cycle\ORM\Collection\Pivoted\PivotedCollection;
use Cycle\ORM\Entity\Behavior;
use DateTimeImmutable;

#[Entity(repository: \App\Blog\Tag\TagRepository::class)]
#[Index(columns: ['label'], unique: true)]
#[Behavior\CreatedAt(field: 'created_at', column: 'created_at')]
class Tag
{
    #[Column(type: 'primary')]
    private ?int $id = null;

    #[Column(type: 'string(191)')]
    private string $label;

    #[Column(type: 'datetime')]
    private DateTimeImmutable $created_at;

    /**
     * @var PivotedCollection<array-key, Post, PostTag>
     */
    #[ManyToMany(target: Post::class, though: PostTag::class, fkAction: 'CASCADE', indexCreate: false)]
    private PivotedCollection $posts;

    public function __construct(string $label)
    {
        $this->label = $label;
        $this->created_at = new DateTimeImmutable();
        $this->posts = new PivotedCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->created_at;
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
}
