<?php

declare(strict_types=1); 

namespace App\Invoice\InvItemAmount;

use App\Invoice\Entity\InvItemAmount;
use Cycle\ORM\Select;
use Throwable;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Cycle\Data\Reader\EntityReader;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

final class InvItemAmountRepository extends Select\Repository
{
private EntityWriter $entityWriter;

    public function __construct(Select $select, EntityWriter $entityWriter)
    {
        $this->entityWriter = $entityWriter;
        parent::__construct($select);
    }

    /**
     * Get invitemamounts  without filter
     *
     * @psalm-return DataReaderInterface<int,InvItemAmount>
     */
    public function findAllPreloaded(): DataReaderInterface
    {
        $query = $this->select()->load('inv_item');
        return $this->prepareDataReader($query);
    }
    
    /**
     * @psalm-return DataReaderInterface<int, InvItemAmount>
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
    public function save(InvItemAmount $invitemamount): void
    {
        $this->entityWriter->write([$invitemamount]);
    }
    
    /**
     * @throws Throwable
     */
    public function delete(InvItemAmount $invitemamount): void
    {
        $this->entityWriter->delete([$invitemamount]);
    }
    
    private function prepareDataReader($query): EntityReader
    {
        return (new EntityReader($query))->withSort(
            Sort::only(['id'])
                ->withOrder(['id' => 'asc'])
        );
    } 
    
    public function repoInvItemAmountquery(string $inv_item_id): InvItemAmount {
        $query = $this->select()->load(['inv_item'])->where(['inv_item_id' => $inv_item_id]);
        return  $query->fetchOne();        
    }
    
    /**
     * Determine if a inv amount summary exists for a specific inv item
     * @param string $inv_item_id 
     * @psalm-return DataReaderInterface<int,InvItemAmount>
     */
    public function repoCount(string $inv_item_id): int {
        $query = $this->select()
                      ->where(['inv_item_id'=>$inv_item_id]);
        return $query->count(); 
    }
}