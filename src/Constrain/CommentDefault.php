<?php

namespace App\Constrain;

use Cycle\ORM\Select\ConstrainInterface;
use Cycle\ORM\Select\QueryBuilder;

/**
 * Not deleted
 * Public with condition
 * Sorted
 */
class CommentDefault implements ConstrainInterface
{
    private ?array $publicOrCondition;

    public function __construct(?array $publicOrCondition = null)
    {
        $this->publicOrCondition = $publicOrCondition;
    }

    public function apply(QueryBuilder $query): void
    {
        // not deleted
        $query->where('deleted_at', '=', null);
        // public or...
        if ($this->publicOrCondition !== null) {
            $query->where([
                '@or' => [
                    ['public' => true],
                    $this->publicOrCondition,
                ]
            ]);
        } else {
            $query->andWhere('public', '=', true);
        }
        // sort
        $query->orderBy('published_at', 'DESC');
    }
}
