<?php

declare(strict_types=1); 

namespace App\Invoice\Invcust;

use App\Invoice\Entity\Invcust;
use Cycle\ORM\Select;
use Throwable;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Cycle\Data\Reader\EntityReader;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

final class InvcustRepository extends Select\Repository
{
private EntityWriter $entityWriter;

    public function __construct(Select $select, EntityWriter $entityWriter)
    {
        $this->entityWriter = $entityWriter;
        parent::__construct($select);
    }

    /**
     * Get invcusts  without filter
     *
     * @psalm-return DataReaderInterface<int,Invcust>
     */
    public function findAllPreloaded(): DataReaderInterface
    {
        $query = $this->select();
        return $this->prepareDataReader($query);
    }
    
    /**
     * @psalm-return DataReaderInterface<int, Invcust>
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
    public function save(Invcust $invcust): void
    {
        $this->entityWriter->write([$invcust]);
    }
    
    /**
     * @throws Throwable
     */
    public function delete(Invcust $invcust): void
    {
        $this->entityWriter->delete([$invcust]);
    }
    
    private function prepareDataReader($query): EntityReader
    {
        return (new EntityReader($query))->withSort(
            Sort::only(['id'])
                ->withOrder(['id' => 'asc'])
        );
    }
    
    public function repoInvcustquery(string $id): Invcust    {
        $query = $this->select()->load('inv')->where(['id' => $id]);
        return  $query->fetchOne();        
    }
}