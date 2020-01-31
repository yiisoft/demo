<?php

namespace App\Blog\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

/**
 * @Entity
 */
class PostTag
{
    /**
     * @Column(type="primary")
     * @var int
     */
    private $id;
}
