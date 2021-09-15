<?php

declare(strict_types=1); 

namespace App\Invoice\ItemLookup;

use App\Invoice\Entity\ItemLookup;
use Cycle\ORM\Select;
use Throwable;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Cycle\Data\Reader\EntityReader;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

final class ItemLookupRepository extends Select\Repository
{
private EntityWriter $entityWriter;

    public function __construct(Select $select, EntityWriter $entityWriter)
    {
        $this->entityWriter = $entityWriter;
        parent::__construct($select);
    }

    /**
     * Get itemlookups  without filter
     *
     * @psalm-return DataReaderInterface<int,ItemLookup>
     */
    public function findAllPreloaded(): DataReaderInterface
    {
        $query = $this->select();
        return $this->prepareDataReader($query);
    }
    
    /**
     * @psalm-return DataReaderInterface<int, ItemLookup>
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
    public function save(ItemLookup $itemlookup): void
    {
        $this->entityWriter->write([$itemlookup]);
    }
    
    /**
     * @throws Throwable
     */
    public function delete(ItemLookup $itemlookup): void
    {
        $this->entityWriter->delete([$itemlookup]);
    }
    
    private function prepareDataReader($query): EntityReader
    {
        return (new EntityReader($query))->withSort(
            Sort::only(['id'])
                ->withOrder(['id' => 'asc'])
        );
    }
    
    public function repoItemLookupquery(string $id): ItemLookup    {
        $query = $this->select()->where(['id' => $id]);
        return  $query->fetchOne();        
    }
}