<?php

declare(strict_types=1);

namespace App\Invoice\Client\Scope;

use Cycle\ORM\Select\ConstrainInterface;
use Cycle\ORM\Select\QueryBuilder;

class ActiveScope implements ConstrainInterface
{
    public function apply(QueryBuilder $query): void
    {
        // active only
        $query->where(['client_active' => true]);
    }
}
