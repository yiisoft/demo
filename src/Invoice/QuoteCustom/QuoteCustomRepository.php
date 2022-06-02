<?php

declare(strict_types=1); 

namespace App\Invoice\QuoteCustom;

use App\Invoice\Entity\QuoteCustom;
use Cycle\ORM\Select;
use Throwable;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Cycle\Data\Reader\EntityReader;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

final class QuoteCustomRepository extends Select\Repository
{
private EntityWriter $entityWriter;

    public function __construct(Select $select, EntityWriter $entityWriter)
    {
        $this->entityWriter = $entityWriter;
        parent::__construct($select);
    }

    /**
     * Get quotecustoms  without filter
     *
     * @psalm-return DataReaderInterface<int,QuoteCustom>
     */
    public function findAllPreloaded(): DataReaderInterface
    {
        $query = $this->select()->load('custom_field')->load('quote');
        return $this->prepareDataReader($query);
    }
    
    /**
     * @psalm-return DataReaderInterface<int, QuoteCustom>
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
    public function save(QuoteCustom $quotecustom): void
    {
        $this->entityWriter->write([$quotecustom]);
    }
    
    /**
     * @throws Throwable
     */
    public function delete(QuoteCustom $quotecustom): void
    {
        $this->entityWriter->delete([$quotecustom]);
    }
    
    private function prepareDataReader($query): EntityReader
    {
        return (new EntityReader($query))->withSort(
            Sort::only(['id'])
                ->withOrder(['id' => 'asc'])
        );
    }
    
    public function repoQuoteCustomquery(string $id): QuoteCustom    {
        $query = $this->select()->load('custom_field')
                                ->load('quote')
                                ->where(['id'=>$id]);
        return  $query->fetchOne();        
    }
    
    
    public function repoFormValuequery(string $quote_id, string $custom_field_id): QuoteCustom {
        $query = $this->select()->where(['quote_id' =>$quote_id])
                                ->andWhere(['custom_field_id' =>$custom_field_id]);
        return  $query->fetchOne();        
    }
    
    public function repoQuoteCustomCount(string $quote_id, string $custom_field_id) : int {
        $query = $this->select()->where(['quote_id' =>$quote_id])
                                ->andWhere(['custom_field_id' =>$custom_field_id]);
        return $query->count();
    } 
    
    public function repoQuoteCount(string $quote_id) : int {
        $query = $this->select()->where(['quote_id' =>$quote_id]);
        return $query->count();
    }   
    
    /**
     * Get all fields that have been setup for a particular quote
     *
     * @psalm-return DataReaderInterface<int,QuoteCustom>
     */
    public function repoFields(string $quote_id): DataReaderInterface
    {
        $query = $this->select()->where(['quote_id'=>$quote_id]);                
        return $this->prepareDataReader($query);
    }
}