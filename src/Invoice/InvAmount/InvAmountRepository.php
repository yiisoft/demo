<?php

declare(strict_types=1); 

namespace App\Invoice\InvAmount;

use App\Invoice\Entity\InvAmount;
use Cycle\ORM\Select;
use Throwable;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Cycle\Data\Reader\EntityReader;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

final class InvAmountRepository extends Select\Repository
{
private EntityWriter $entityWriter;

    public function __construct(Select $select, EntityWriter $entityWriter)
    {
        $this->entityWriter = $entityWriter;
        parent::__construct($select);
    }

    /**
     * Get amounts  without filter
     *
     * @psalm-return DataReaderInterface<int,InvAmount>
     */
    public function findAllPreloaded(): DataReaderInterface
    {
        $query = $this->select();
        return $this->prepareDataReader($query);
    }
    
    /**
     * @psalm-return DataReaderInterface<int, InvAmount>
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
    public function save(InvAmount $amount): void
    {
        $this->entityWriter->write([$amount]);
    }
    
    /**
     * @throws Throwable
     */
    public function delete(InvAmount $amount): void
    {
        $this->entityWriter->delete([$amount]);
    }
    
    private function prepareDataReader($query): EntityReader
    {
        return (new EntityReader($query))->withSort(
            Sort::only(['id'])
                ->withOrder(['id' => 'asc'])
        );
    }
    
    public function repoInvAmountquery(string $id): InvAmount    {
        $query = $this->select()->load('inv')->where(['id' => $id]);
        return  $query->fetchOne();        
    }
}