<?php

declare(strict_types=1); 

namespace App\Invoice\QuoteItem;

use App\Invoice\Entity\QuoteItem;
use Cycle\ORM\Select;
use Throwable;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Cycle\Data\Reader\EntityReader;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

final class QuoteItemRepository extends Select\Repository
{
private EntityWriter $entityWriter;

    public function __construct(Select $select, EntityWriter $entityWriter)
    {
        $this->entityWriter = $entityWriter;
        parent::__construct($select);
    }

    /**
     * Get quoteitems  without filter
     *
     * @psalm-return DataReaderInterface<int,QuoteItem>
     */
    public function findAllPreloaded(): DataReaderInterface
    {
        $query = $this->select();
        return $this->prepareDataReader($query);
    }
    
    /**
     * @psalm-return DataReaderInterface<int, QuoteItem>
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
    public function save(QuoteItem $quoteitem): void
    {
        $this->entityWriter->write([$quoteitem]);
    }
    
    /**
     * @throws Throwable
     */
    public function delete(QuoteItem $quoteitem): void
    {
        $this->entityWriter->delete([$quoteitem]);
    }
    
    private function prepareDataReader($query): EntityReader
    {
        return (new EntityReader($query))->withSort(
            Sort::only(['id'])
                ->withOrder(['id' => 'asc'])
        );
    }
    
    public function repoQuoteItemquery(string $id): QuoteItem    {
        $query = $this->select()->load('tax_rate')->load('product')->load('quote')->where(['id' => $id]);
        return  $query->fetchOne();        
    }
}