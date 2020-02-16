<?php

namespace App\Blog\Post\Scope;

use Cycle\ORM\Select\ConstrainInterface;
use Cycle\ORM\Select\QueryBuilder;

class PublicScope implements ConstrainInterface
{
    public function apply(QueryBuilder $query): void
    {
        // public only
        $query->where(['public' => true]);
    }
}
