<?php

declare(strict_types=1);

namespace App\Invoice\Client;

use App\Invoice\Entity\Client;
use Cycle\ORM\Command\CommandInterface;
use Cycle\ORM\Command\ContextCarrierInterface;
use Cycle\ORM\Command\Database\Update;
use Cycle\ORM\Context\ConsumerInterface;
use Cycle\ORM\Heap\Node;
use Cycle\ORM\Heap\State;
use Cycle\ORM\Mapper\Mapper;

final class ClientMapper extends Mapper
{
    /**
     * @param Client $entity
     */
    public function queueUpdate($entity, Node $node, State $state): ContextCarrierInterface
    {
        /** @var Update $command */
        $command = parent::queueUpdate($entity, $node, $state);

        $now = new \DateTimeImmutable();

        $state->register('client_date_modified', $now, true);
        $command->registerAppendix('client_date_modified', $now);

        $this->touch($entity, $node, $state, $command);

        return $command;
    }

    /**
     * @param Client $entity
     */
    public function queueDelete($entity, Node $node, State $state): CommandInterface
    {
        // identify entity as being "deleted"
        $state->setStatus(Node::SCHEDULED_DELETE);
        $state->decClaim();

        $command = new Update(
            $this->source->getDatabase(),
            $this->source->getTable(),
            [
                'client_date_modified' => new \DateTimeImmutable(),
                'client_active' => false,
            ]
        );

        // forward primaryKey value from entity state
        // this sequence is only required if the entity is created and deleted
        // within one transaction
        $command->waitScope($this->primaryColumn);
        $state->forward(
            $this->primaryKey,
            $command,
            $this->primaryColumn,
            true,
            ConsumerInterface::SCOPE
        );

        return $command;
    }

    private function touch(Client $entity, Node $node, State $state, ContextCarrierInterface $command)
    {
        $now = new \DateTimeImmutable();

        if ($entity->isNewRecord()) {
            $state->register('client_date_created', $now, true);
            $command->register('client_date_created', $now, true);
            $state->register('client_date_modified', $now, true);
            $command->register('client_date_modified', $now, true);
        }
    }
}
