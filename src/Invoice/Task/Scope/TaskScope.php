<?php

declare(strict_types=1);

namespace App\Invoice\Task\Scope;

use Cycle\ORM\Select\ConstrainInterface;
use Cycle\ORM\Select\QueryBuilder;

class TaskScope implements ConstrainInterface
{
    public function apply(QueryBuilder $query): void
    {
        //task_status only
        $query->where(['task_status' => true]);
    }
}
