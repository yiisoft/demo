<?php

namespace App\Constrain;

use Cycle\ORM\Select\ConstrainInterface;
use Cycle\ORM\Select\QueryBuilder;

class PostPublic implements ConstrainInterface
{

    public function apply(QueryBuilder $query): void
    {
        // public only
        $query->where(['public' => true]);
    }
}
