<?php

declare(strict_types=1); 

namespace App\Invoice\InvCustom;

use App\Invoice\Entity\InvCustom;
use Cycle\ORM\Select;
use Throwable;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Cycle\Data\Reader\EntityReader;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

final class InvCustomRepository extends Select\Repository
{
private EntityWriter $entityWriter;

    public function __construct(Select $select, EntityWriter $entityWriter)
    {
        $this->entityWriter = $entityWriter;
        parent::__construct($select);
    }

    /**
     * Get invcustoms  without filter
     *
     * @psalm-return DataReaderInterface<int,InvCustom>
     */
    public function findAllPreloaded(): DataReaderInterface
    {
        $query = $this->select()->load('custom_field')->load('inv');
        return $this->prepareDataReader($query);
    }
    
    /**
     * @psalm-return DataReaderInterface<int, InvCustom>
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
    public function save(InvCustom $invcustom): void
    {
        $this->entityWriter->write([$invcustom]);
    }
    
    /**
     * @throws Throwable
     */
    public function delete(InvCustom $invcustom): void
    {
        $this->entityWriter->delete([$invcustom]);
    }
    
    private function prepareDataReader($query): EntityReader
    {
        return (new EntityReader($query))->withSort(
            Sort::only(['id'])
                ->withOrder(['id' => 'asc'])
        );
    }
    
    public function repoInvCustomquery(string $id): InvCustom    {
        $query = $this->select()->load('custom_field')
                                ->load('inv')
                                ->where(['id'=>$id]);
        return  $query->fetchOne();        
    }
    
    
    public function repoFormValuequery(string $inv_id, string $custom_field_id): InvCustom {
        $query = $this->select()->where(['inv_id' =>$inv_id])
                                ->andWhere(['custom_field_id' =>$custom_field_id]);
        return  $query->fetchOne();        
    }
    
    public function repoInvCustomCount(string $inv_id, string $custom_field_id) : int {
        $query = $this->select()->where(['inv_id' =>$inv_id])
                                ->andWhere(['custom_field_id' =>$custom_field_id]);
        return $query->count();
    } 
    
    public function repoInvCount(string $inv_id) : int {
        $query = $this->select()->where(['inv_id' =>$inv_id]);
        return $query->count();
    }   
    
    /**
     * Get all fields that have been setup for a particular inv
     *
     * @psalm-return DataReaderInterface<int,InvCustom>
     */
    public function repoFields(string $inv_id): DataReaderInterface
    {
        $query = $this->select()->where(['inv_id'=>$inv_id]);                
        return $this->prepareDataReader($query);
    }
}