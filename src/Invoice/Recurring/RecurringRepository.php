<?php

declare(strict_types=1); 

namespace App\Invoice\Recurring;

use App\Invoice\Entity\Recurring;
use Cycle\ORM\Select;
use Throwable;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Cycle\Data\Reader\EntityReader;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

final class RecurringRepository extends Select\Repository
{
private EntityWriter $entityWriter;

    public function __construct(Select $select, EntityWriter $entityWriter)
    {
        $this->entityWriter = $entityWriter;
        parent::__construct($select);
    }

    /**
     * Get recurrings  without filter
     *
     * @psalm-return DataReaderInterface<int,Recurring>
     */
    public function findAllPreloaded(): DataReaderInterface
    {
        $query = $this->select();
        return $this->prepareDataReader($query);
    }
    
    /**
     * @psalm-return DataReaderInterface<int, Recurring>
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
    public function save(Recurring $recurring): void
    {
        $this->entityWriter->write([$recurring]);
    }
    
    /**
     * @throws Throwable
     */
    public function delete(Recurring $recurring): void
    {
        $this->entityWriter->delete([$recurring]);
    }
    
    private function prepareDataReader($query): EntityReader
    {
        return (new EntityReader($query))->withSort(
            Sort::only(['id'])
                ->withOrder(['id' => 'asc'])
        );
    }
    
    public function repoRecurringquery(string $id): Recurring    {
        $query = $this->select()->load('inv')->where(['id' => $id]);
        return  $query->fetchOne();        
    }
}