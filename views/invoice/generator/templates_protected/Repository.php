<?php
   echo "<?php\n";             
?>

declare(strict_types=1); 

namespace <?= $generator->getNamespace_path() .DIRECTORY_SEPARATOR. $generator->getCamelcase_capital_name().';'."\n"; ?>

use <?= $generator->getNamespace_path() .DIRECTORY_SEPARATOR.'Entity' .DIRECTORY_SEPARATOR.$generator->getCamelcase_capital_name().';'."\n"; ?>
use Cycle\ORM\Select;
use Throwable;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Cycle\Data\Reader\EntityReader;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

final class <?= $generator->getCamelcase_capital_name(); ?>Repository extends Select\Repository
{
private EntityWriter $entityWriter;

    public function __construct(Select $select, EntityWriter $entityWriter)
    {
        $this->entityWriter = $entityWriter;
        parent::__construct($select);
    }

    /**
     * Get <?= $generator->getSmall_singular_name(); ?>s  without filter
     *
     * @psalm-return DataReaderInterface<int,<?= $generator->getCamelcase_capital_name(); ?>>
     */
    public function findAllPreloaded(): DataReaderInterface
    {
        <?php if (!empty($relations)) {
            echo '$query = $this->select()';
            foreach ($relations as $relation) {
                    echo "->load('".$relation->getLowercase_name()."')"."\n";
            }
        } else {
            echo '$query = $this->select();';    
        }
        ?>
        return $this->prepareDataReader($query);
    }
    
    /**
     * @psalm-return DataReaderInterface<int, <?= $generator->getCamelcase_capital_name(); ?>>
     */
    public function getReader(): DataReaderInterface
    {
        return (new EntityReader($this->select()))
            ->withSort($this->getSort());
    }
    
    private function getSort(): Sort
    {
        return Sort::only(['id'])->withOrder(['id' => 'asc']);
    }
    
    /**
     * @throws Throwable
     */
    public function save(<?= $generator->getCamelcase_capital_name(); ?> $<?= $generator->getSmall_singular_name(); ?>): void
    {
        $this->entityWriter->write([$<?= $generator->getSmall_singular_name(); ?>]);
    }
    
    /**
     * @throws Throwable
     */
    public function delete(<?= $generator->getCamelcase_capital_name(); ?> $<?= $generator->getSmall_singular_name(); ?>): void
    {
        $this->entityWriter->delete([$<?= $generator->getSmall_singular_name(); ?>]);
    }
    
    private function prepareDataReader($query): EntityReader
    {
        return (new EntityReader($query))->withSort(
            Sort::only(['id'])
                ->withOrder(['id' => 'asc'])
        );
    }
    
    public function repo<?= $generator->getCamelcase_capital_name(); ?>query(string $id): <?= $generator->getCamelcase_capital_name(); ?>
    {
        <?php if (!empty($relations)) {
            echo '$query = $this->select()';
            foreach ($relations as $relation) {
                    echo "->load('".$relation->getLowercase_name()."')"."\n";
                    echo "->where(['id' =>".'$id]);';
            }
        } else {
            echo '$query = $this->select()'."\n";
            echo "->where(['id' =>".'$id]);';
        }
        ?>
        return  $query->fetchOne();        
    }
}