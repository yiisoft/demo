<?php

declare(strict_types=1); 

namespace App\Invoice\QuoteTaxRate;

use App\Invoice\Entity\QuoteTaxRate;
use Cycle\ORM\Select;
use Throwable;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Cycle\Data\Reader\EntityReader;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

final class QuoteTaxRateRepository extends Select\Repository
{
private EntityWriter $entityWriter;

    public function __construct(Select $select, EntityWriter $entityWriter)
    {
        $this->entityWriter = $entityWriter;
        parent::__construct($select);
    }

    /**
     * Get quotetaxrates  without filter
     *
     * @psalm-return DataReaderInterface<int,QuoteTaxRate>
     */
    public function findAllPreloaded(): DataReaderInterface
    {
        $query = $this->select()->load('quote')->load('tax_rate');
        return $this->prepareDataReader($query);
    }
    
    /**
     * @psalm-return DataReaderInterface<int, QuoteTaxRate>
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
    public function save(QuoteTaxRate $quotetaxrate): void
    {
        $this->entityWriter->write([$quotetaxrate]);
    }
    
    /**
     * @throws Throwable
     */
    public function delete(QuoteTaxRate $quotetaxrate): void
    {
        $this->entityWriter->delete([$quotetaxrate]);
    }
    
    private function prepareDataReader($query): EntityReader
    {
        return (new EntityReader($query))->withSort(
            Sort::only(['id'])
                ->withOrder(['id' => 'asc'])
        );
    }
    
    //find all quote tax rates assigned to specific quote. Normally only one but just in case more than one assigned
    //used in quote/view to determine if a 'one-off'  quote tax rate acquired from tax rates is to be applied to the quote 
    //quote tax rates are children of their parent tax rate and are normally used when all products use the same tax rate ie. no item tax
    public function repoCount($quote_id): int {
        $count = $this->select()
                      ->where(['quote_id' => $quote_id])
                      ->count();
        return $count;   
    }
    
    //find a specific quotes tax rate, normally to delete
    public function repoQuoteTaxRatequery(string $id): QuoteTaxRate    {
        $query = $this->select()->load('quote')->load('tax_rate')->where(['id' => $id]);
        return  $query->fetchOne();        
    }
    
    // find all quote tax rates used for a specific quote normally to apply include_item_tax 
    // (see function calculate_quote_taxes in NumberHelper
    // load 'tax rate' so that we can use tax_rate_id through the BelongTo relation in the Entity
    // to access the parent tax rate table's percent name and percentage 
    // which we will use in quote/view
    public function repoQuotequery(string $quote_id): DataReaderInterface    {
        $query = $this->select()->load('tax_rate')->where(['quote_id' => $quote_id]);
        return $this->prepareDataReader($query);   
    }
    
    public function repoTaxRatequery(string $tax_rate_id): QuoteTaxRate    {
        $query = $this->select()->load('tax_rate')->where(['tax_rate_id' => $tax_rate_id]);
        return  $query->fetchOne();        
    }
        
    public function repoGetQuoteTaxRateAmounts(string $quote_id): DataReaderInterface  {
        $query = $this->select()
                      ->where(['quote_id'=>$quote_id]);
        return $this->prepareDataReader($query);   
    }
        
    public function repoUpdateQuoteTaxTotal(string $quote_id): float {
        $getTaxRateAmounts = $this->repoGetQuoteTaxRateAmounts($quote_id);        
        $total = 0.00;
        foreach ($getTaxRateAmounts as $item) {
            foreach ($item as $key=>$value) {
               if ($key === 'quote_tax_rate_amount') {             
                  $total += $value;  
               } 
            }    
        }
        return $total;
    }
}