<?php

declare(strict_types=1);

namespace App\Invoice\Quote;

use App\Invoice\Entity\Quote;
use Cycle\ORM\Command\CommandInterface;
use Cycle\ORM\Command\ContextCarrierInterface;
use Cycle\ORM\Command\Database\Update;
use Cycle\ORM\Context\ConsumerInterface;
use Cycle\ORM\Heap\Node;
use Cycle\ORM\Heap\State;
use Cycle\ORM\Mapper\Mapper;

final class QuoteMapper extends Mapper
{
    /**
     * @param Client $entity
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
    
    private function touch(Quote $entity, Node $node, State $state, ContextCarrierInterface $command)
    {
        $now = new \DateTimeImmutable();

        if ($entity->isNewRecord()) {
            $state->register('date_created', $now, true);
            $command->register('date_created', $now, true);
            $state->register('date_modified', $now, true);
            $command->register('date_modified', $now, true);
        }
    }          
}

?>