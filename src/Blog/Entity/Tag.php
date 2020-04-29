<?php

declare(strict_types=1);

namespace App\Blog\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\ManyToMany;
use Cycle\Annotated\Annotation\Table;
use Cycle\Annotated\Annotation\Table\Index;
use Cycle\ORM\Relation\Pivoted\PivotedCollection;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity(repository="App\Blog\Tag\TagRepository", mapper="App\Blog\Tag\TagMapper")
 * @Table(
 *     indexes={
 *         @Index(columns={"label"}, unique=true)
 *     }
 * )
 */
class Tag
{
    /**
     * @Column(type="primary")
     */
    private ?int $id = null;

    /**
     * @Column(type="string(255)")
     */
    private string $label;

    /**
     * @Column(type="datetime")
     */
    private DateTimeImmutable $created_at;

    /**
     * @ManyToMany(target="App\Blog\Entity\Post", though="PostTag", fkAction="CASCADE", indexCreate=false)
     * @var Post[]|PivotedCollection
     */
    private $posts;

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
