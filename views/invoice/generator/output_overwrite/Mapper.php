<?php

declare(strict_types=1);

namespace App\Invoice\Product;

use App\Invoice\Entity\Product;
use Cycle\ORM\Command\CommandInterface;
use Cycle\ORM\Command\ContextCarrierInterface;
use Cycle\ORM\Command\Database\Update;
use Cycle\ORM\Context\ConsumerInterface;
use Cycle\ORM\Heap\Node;
use Cycle\ORM\Heap\State;
use Cycle\ORM\Mapper\Mapper;

final class ProductMapper extends Mapper
{
    
   
  /**
   * @param Product $entity
   */
   public function queueUpdate($entity, Node $node, State $state): ContextCarrierInterface
   {
      /** @var Update $command */
     $command = parent::queueUpdate($entity, $node, $state);

      $now = new \DateTimeImmutable();
      $state->register(updated_at, $now, true);
      $command->registerAppendix(updated_at, $now);


      $this->touch($entity, $node, $state, $command);

      return $command;
   }
    
     /**
      * @param Product $entity
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
              'deleted_at' => new \DateTimeImmutable(),
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
      private function touch(Product$entity, Node $node, State $state, ContextCarrierInterface $command)
     {
       $now = new \DateTimeImmutable();
       if ($entity->getCreated_at() === null) {
          $state->register(created_at, $now, true);
          $command->register(created_at, $now, true);
       }
     }
    
}

?>