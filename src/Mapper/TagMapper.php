<?php

namespace App\Mapper;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Table;
use Cycle\ORM\Command\ContextCarrierInterface;
use Cycle\ORM\Command\CommandInterface;
use Cycle\ORM\Command\Database\Update;
use Cycle\ORM\Context\ConsumerInterface;
use Cycle\ORM\Heap\Node;
use Cycle\ORM\Heap\State;
use Cycle\ORM\Mapper\Mapper;

/**
 * @Table(
 *      columns={
 *          "created_at": @Column(type="datetime")
 *      }
 * )
 */
class TagMapper extends Mapper
{
    public function queueCreate($entity, Node $node, State $state): ContextCarrierInterface
    {
        $command = parent::queueCreate($entity, $node, $state);

        $state->register('created_at', new \DateTimeImmutable(), true);
        $command->register('created_at', new \DateTimeImmutable(), true);

        return $command;
    }
}
