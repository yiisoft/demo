<?php 
   echo "<?php\n";             
?>

declare(strict_types=1);

namespace <?= $generator->getNamespace_path().DIRECTORY_SEPARATOR.$generator->getCamelcase_capital_name().'\Scope'; ?>;

use Cycle\ORM\Select\ConstrainInterface;
use Cycle\ORM\Select\QueryBuilder;

class <?= ucfirst($generator->getConstrain_index_field()); ?>Scope implements ConstrainInterface
{
    public function apply(QueryBuilder $query): void
    {
        // <?= lcfirst($generator->getConstrain_index_field()); ?> only
        $query->where(['<?= lcfirst($generator->getConstrain_index_field()); ?>' => true]);
    }
}
