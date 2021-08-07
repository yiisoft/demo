<?php  
   echo "<?php\n";             
?>

declare(strict_types=1); 

namespace <?= $generator->getNamespace_path() .DIRECTORY_SEPARATOR. $generator->getCamelcase_capital_name().";\n"; ?>

use <?= $generator->getNamespace_path() .DIRECTORY_SEPARATOR.'Entity' .DIRECTORY_SEPARATOR.$generator->getCamelcase_capital_name().";\n"; ?>


final class <?= $generator->getCamelcase_capital_name(); ?>Service
{

    private <?= $generator->getCamelcase_capital_name(); ?>Repository $repository;

    public function __construct(<?= $generator->getCamelcase_capital_name(); ?>Repository $repository)
    {
        $this->repository = $repository;
    }

    public function save<?= $generator->getCamelcase_capital_name(); ?>(<?= $generator->getCamelcase_capital_name(); ?> $model, <?= $generator->getCamelcase_capital_name(); ?>Form $form): void
    {
        <?php
            echo "\n";
            foreach ($orm_schema->getColumns() as $column) { 
                if (($column->getAbstractType() <> 'primary')) {
                    echo '       $model->set'. ucfirst($column->getName()).'($form->get'.ucfirst($column->getName()).'());'."\n";
                }    
            }
        ?> 
        $this->repository->save($model);
    }
    
    public function delete<?= $generator->getCamelcase_capital_name(); ?>(<?= $generator->getCamelcase_capital_name(); ?> $model): void
    {
        $this->repository->delete($model);
    }
}