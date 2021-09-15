<?php

declare(strict_types=1); 

namespace App\Invoice\Quote;

use App\Invoice\Entity\Quote;
use Cycle\ORM\Select;
use Throwable;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Cycle\Data\Reader\EntityReader;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

final class QuoteRepository extends Select\Repository
{
private EntityWriter $entityWriter;

    public function __construct(Select $select, EntityWriter $entityWriter)
    {
        $this->entityWriter = $entityWriter;
        parent::__construct($select);
    }

    /**
     * Get quotes  without filter
     *
     * @psalm-return DataReaderInterface<int,Quote>
     */
    public function findAllPreloaded(): DataReaderInterface
    {
        $query = $this->select()
                ->load('client')
                ->load('group')
                ->load('user');
        return $this->prepareDataReader($query);
    }
    
    /**
     * @psalm-return DataReaderInterface<int, Quote>
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
    public function save(Quote $quote): void
    {
        $this->entityWriter->write([$quote]);
    }
    
    /**
     * @throws Throwable
     */
    public function delete(Quote $quote): void
    {
        $this->entityWriter->delete([$quote]);
    }
    
    private function prepareDataReader($query): EntityReader
    {
        return (new EntityReader($query))->withSort(
            Sort::only(['id'])
                ->withOrder(['id' => 'asc'])
        );
    }
    
    public function repoQuotequery(string $id): Quote    {
        $query = $this->select()->load('client')->load('group')->load('user')->where(['id' => $id]);
        return  $query->fetchOne();        
    }
}