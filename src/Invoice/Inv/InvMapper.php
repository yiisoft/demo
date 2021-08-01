<?php

declare(strict_types=1);

namespace App\Invoice\Inv;

use App\Invoice\Entity\Inv;
use Cycle\ORM\Command\ContextCarrierInterface;
use Cycle\ORM\Command\Database\Update;
use Cycle\ORM\Heap\Node;
use Cycle\ORM\Heap\State;
use Cycle\ORM\Mapper\Mapper;

final class InvMapper extends Mapper
{
    /**
     * @param Inv $entity
     */
    public function queueUpdate($entity, Node $node, State $state): ContextCarrierInterface
    {
        /** @var Update $command */
        $command = parent::queueUpdate($entity, $node, $state);

        $now = new \DateTimeImmutable();

        $state->register('date_modified', $now, true);
        $command->registerAppendix('date_modified', $now);

        $this->touch($entity, $node, $state, $command);

        return $command;
    }

    private function touch(Client $entity, Node $node, State $state, ContextCarrierInterface $command)
    {
        $now = new \DateTimeImmutable();

        if ($entity->isNewRecord()) {
            $state->register('date_modified', $now, true);
            $command->register('date_modified', $now, true);
        }
    }
}
