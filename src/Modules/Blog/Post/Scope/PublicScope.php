<?php

declare(strict_types=1);

namespace App\Modules\Blog\Post\Scope;

use Cycle\ORM\Select\QueryBuilder;
use Cycle\ORM\Select\ScopeInterface as ConstrainInterface;

final class PublicScope implements ConstrainInterface
{
    public function apply(QueryBuilder $query): void
    {
        // public only
        $query->where(['public' => true]);
    }
}
