<?php

declare(strict_types=1); 

namespace App\Invoice\InvItem;

use App\Invoice\Entity\InvItem;
use Cycle\ORM\Select;
use Spiral\Database\Injection\Parameter;
use Throwable;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Cycle\Data\Reader\EntityReader;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

final class InvItemRepository extends Select\Repository
{
private EntityWriter $entityWriter;

    public function __construct(Select $select, EntityWriter $entityWriter)
    {
        $this->entityWriter = $entityWriter;
        parent::__construct($select);
    }

    /**
     * Get invitems  without filter
     *
     * @psalm-return DataReaderInterface<int,InvItem>
     */
    public function findAllPreloaded(): DataReaderInterface
    {
        $query = $this->select()
                      ->load(['tax_rate','product','inv']);
        return $this->prepareDataReader($query);
    }
    
    /**
     * @psalm-return DataReaderInterface<int, InvItem>
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
    public function save(InvItem $invitem): void
    {
        $this->entityWriter->write([$invitem]);
    }
    
    /**
     * @throws Throwable
     */
    public function delete(InvItem $invitem): void
    {
        $this->entityWriter->delete([$invitem]);
    }
    
    private function prepareDataReader($query): EntityReader
    {
        return (new EntityReader($query))->withSort(
            Sort::only(['id'])
                ->withOrder(['id' => 'asc'])
        );
    }
    
    public function repoInvItemquery(string $id): InvItem    {
        $query = $this->select()->load(['tax_rate','product','inv'])->where(['id' => $id]);
        return  $query->fetchOne();        
    }
    
    
    /**
     * Get all items id's that belong to a specific inv
     *
     * @psalm-return DataReaderInterface<int, InvItem>
     */
    public function repoInvItemIdquery(string $inv_id):  DataReaderInterface {
        $query = $this->select('id','order asc')
                      ->load(['tax_rate','product','inv'])
                      ->where(['inv_id' => $inv_id]);
        return $this->prepareDataReader($query); 
    }
    
    /**
     * Get all items belonging to inv
     *
     * @psalm-return DataReaderInterface<int, InvItem>
     */
    public function repoInvquery(string $inv_id): DataReaderInterface { 
        $query = $this->select()
                      ->load(['tax_rate','product','inv'])
                      ->where(['inv_id' => $inv_id]);                                
        return $this->prepareDataReader($query);        
    }
    
    public function repoCount(string $inv_id) : int {
        $count = $this->select()
                      ->where(['inv_id' => $inv_id])                                
                      ->count();
        return $count; 
    }
    
    public function repoInvItemCount(string $id) : int {
        $count = $this->select()
                      ->where(['id' => $id])                                
                      ->count();
        return $count; 
    }
        
    /**
     * Get selection of inv items from all inv_items
     *
     * @psalm-return DataReaderInterface<int, InvItem>
     */
     
    public function findinInvItems($item_ids) : DataReaderInterface {
        $query = $this->select()->where(['id'=>['in'=> new Parameter($item_ids)]]);
        return $this->prepareDataReader($query);    
    } 
}
