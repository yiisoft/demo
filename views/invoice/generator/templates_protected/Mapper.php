<?php
   echo "<?php\n";             
?>

declare(strict_types=1);

namespace <?= $generator->getNamespace_path() .DIRECTORY_SEPARATOR. $generator->getCamelcase_capital_name(); ?>;

use <?= $generator->getNamespace_path() .DIRECTORY_SEPARATOR.'Entity' .DIRECTORY_SEPARATOR.$generator->getCamelcase_capital_name(); ?>;
use Cycle\ORM\Command\CommandInterface;
use Cycle\ORM\Command\ContextCarrierInterface;
use Cycle\ORM\Command\Database\Update;
use Cycle\ORM\Context\ConsumerInterface;
use Cycle\ORM\Heap\Node;
use Cycle\ORM\Heap\State;
use Cycle\ORM\Mapper\Mapper;

final class <?= $generator->getCamelcase_capital_name(); ?>Mapper extends Mapper
{
    
   <?php if ($generator->isUpdated_include()) {
      echo "\n";
      echo '  /**'."\n";
      echo '   * @param '. $generator->getCamelcase_capital_name(). ' $entity'."\n";
      echo '   */'."\n";
      echo '   public function queueUpdate($entity, Node $node, State $state): ContextCarrierInterface'."\n";
      echo '   {'."\n";
      echo '      /** @var Update $command */'."\n";
      echo '     $command = parent::queueUpdate($entity, $node, $state);'."\n";
      echo "\n";
      echo '      $now = new \DateTimeImmutable();'."\n";
      echo '      $state->register('."updated_at".', $now, true);'."\n";
      echo '      $command->registerAppendix('."updated_at".', $now);'."\n";
      echo "\n"."\n";
      echo '      $this->touch($entity, $node, $state, $command);'."\n";
      echo "\n";
      echo '      return $command;'."\n";
      echo '   }'."\n";
   }
    ?>
    
    <?php if ($generator->isDeleted_include()) {
      echo ' /**'."\n";
      echo '      * @param '. $generator->getCamelcase_capital_name(). ' $entity'."\n";
      echo '     */'."\n";
      echo "\n";
      echo '    public function queueDelete($entity, Node $node, State $state): CommandInterface'."\n";
      echo '    {'."\n";
      echo '      // identify entity as being "deleted"'."\n";
      echo '      $state->setStatus(Node::SCHEDULED_DELETE);'."\n";
      echo '      $state->decClaim();'."\n";
      echo "\n";
      echo '      $command = new Update('."\n";
      echo '      $this->source->getDatabase(),'."\n";
      echo '      $this->source->getTable(),'."\n";
      echo '      ['."\n";
      echo "              'deleted_at' => new \DateTimeImmutable(),"."\n";
      echo '      ]'."\n";
      echo '      );'."\n";
      echo '      // forward primaryKey value from entity state'."\n";
      echo '      // this sequence is only required if the entity is created and deleted'."\n";
      echo '      // within one transaction'."\n";
      echo '      $command->waitScope($this->primaryColumn);'."\n";
      echo '      $state->forward('."\n";
      echo '      $this->primaryKey,'."\n";
      echo '      $command,'."\n";
      echo '      $this->primaryColumn,'."\n";
      echo '      true,'."\n";
      echo '      ConsumerInterface::SCOPE'."\n";
      echo '      );'."\n";
      echo "\n";
      echo '      return $command;'."\n";
      echo '     }'."\n";
    }
    ?>
    <?php if ($generator->isCreated_include()) {
      echo '  private function touch('. $generator->getCamelcase_capital_name(). '$entity, Node $node, State $state, ContextCarrierInterface $command)'."\n";
      echo '     {'."\n";
      echo '       $now = new \DateTimeImmutable();'."\n";
      echo '       if ($entity->getCreated_at() === null) {'."\n";
      echo '          $state->register(', "created_at", ', $now, true);'."\n";
      echo '          $command->register('."created_at". ', $now, true);'."\n";
      echo '       }'."\n";
      echo '     }'."\n";    
    }
    ?>    
}

<?php
   echo "?>";             
?>