<?php

declare(strict_types=1); 

namespace App\Invoice\QuoteAmount;

use App\Invoice\Entity\QuoteAmount;
use Cycle\ORM\Select;
use Throwable;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Cycle\Data\Reader\EntityReader;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

final class QuoteAmountRepository extends Select\Repository
{
private EntityWriter $entityWriter;

    public function __construct(Select $select, EntityWriter $entityWriter)
    {
        $this->entityWriter = $entityWriter;
        parent::__construct($select);
    }

    /**
     * Get quoteamounts  without filter
     *
     * @psalm-return DataReaderInterface<int,QuoteAmount>
     */
    public function findAllPreloaded(): DataReaderInterface
    {
        $query = $this->select();
        return $this->prepareDataReader($query);
    }
    
    /**
     * @psalm-return DataReaderInterface<int, QuoteAmount>
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
    public function save(QuoteAmount $quoteamount): void
    {
        $this->entityWriter->write([$quoteamount]);
    }
    
    /**
     * @throws Throwable
     */
    public function delete(QuoteAmount $quoteamount): void
    {
        $this->entityWriter->delete([$quoteamount]);
    }
    
    private function prepareDataReader($query): EntityReader
    {
        return (new EntityReader($query))->withSort(
            Sort::only(['id'])
                ->withOrder(['id' => 'asc'])
        );
    }
    
    public function repoQuoteAmountquery(string $id): QuoteAmount    {
        $query = $this->select()->load('quote')->where(['id' => $id]);
        return  $query->fetchOne();        
    }
}