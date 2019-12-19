<?php

namespace App\Repository;

use App\Entity\Post;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Select;

class PostRepository extends Select\Repository
{
    public function __construct(ORMInterface $orm, $role = Post::class)
    {
        parent::__construct(new Select($orm, $role));
    }
}
