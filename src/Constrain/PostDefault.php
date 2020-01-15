<?php

namespace App\Constrain;

use Cycle\ORM\Select\ConstrainInterface;
use Cycle\ORM\Select\QueryBuilder;

class PostDefault implements ConstrainInterface
{

    public function apply(QueryBuilder $query): void
    {
        $query->where(['deleted_at' => null, 'public' => true]);
    }
}
