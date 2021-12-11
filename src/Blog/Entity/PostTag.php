<?php

declare(strict_types=1);

namespace App\Blog\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

/**
 * @Entity
 */
final class PostTag
{
    /**
     * @Column(type="primary")
     */
    private ?int $id = null;
    private ?int $post_id = null;
    private ?int $tag_id = null;
}
