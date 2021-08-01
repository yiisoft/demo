<?php

declare(strict_types=1); 

namespace App\Invoice\Sumex;

use App\Invoice\Entity\Sumex;
use Cycle\ORM\Select;
use Throwable;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Cycle\Data\Reader\EntityReader;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

final class SumexRepository extends Select\Repository
{
private EntityWriter $entityWriter;

    public function __construct(Select $select, EntityWriter $entityWriter)
    {
        $this->entityWriter = $entityWriter;
        parent::__construct($select);
    }

    /**
     * Get sumexs  without filter
     *
     * @psalm-return DataReaderInterface<int,Sumex>
     */
    public function findAllPreloaded(): DataReaderInterface
    {
        $query = $this->select();
        return $this->prepareDataReader($query);
    }
    
    /**
     * @psalm-return DataReaderInterface<int, Sumex>
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
    public function save(Sumex $sumex): void
    {
        $this->entityWriter->write([$sumex]);
    }
    
    /**
     * @throws Throwable
     */
    public function delete(Sumex $sumex): void
    {
        $this->entityWriter->delete([$sumex]);
    }
    
    private function prepareDataReader($query): EntityReader
    {
        return (new EntityReader($query))->withSort(
            Sort::only(['id'])
                ->withOrder(['id' => 'asc'])
        );
    }
    
    public function repoSumexquery(string $id): Sumex    {
        $query = $this->select()->where(['id' => $id]);
        return  $query->fetchOne();        
    }
}