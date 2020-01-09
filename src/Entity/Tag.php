<?php

namespace App\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\ManyToMany;
use Cycle\Annotated\Annotation\Table;
use Cycle\Annotated\Annotation\Table\Index;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity(repository="App\Repository\TagRepository", mapper="App\Mapper\TagMapper")
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
     * @var int
     */
    protected $id;

    /**
     * @Column(type="string(255)")
     * @var string
     */
    protected $label;

    /**
     * @Column(type="datetime")
     * @var DateTimeImmutable
     */
    protected $createdAt;

    /**
     * @ManyToMany(target="App\Entity\Post", though="PostTag", fkAction="CASCADE", indexCreate=false)
     * @var ArrayCollection
     */
    protected $posts;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }

    public function getId(): ?string
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
        return $this->createdAt;
    }

    /**
     * @return ArrayCollection|Post[]
     */
    public function getPosts(): ArrayCollection
    {
        return $this->posts;
    }

    public function addPost(Post $post): void
    {
        $this->posts->add($post);
    }
}
