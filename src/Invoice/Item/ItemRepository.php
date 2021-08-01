<?php

declare(strict_types=1); 

namespace App\Invoice\Item;

use App\Invoice\Entity\Item;
use Cycle\ORM\Select;
use Throwable;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Cycle\Data\Reader\EntityReader;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

final class ItemRepository extends Select\Repository
{
    private EntityWriter $entityWriter;

    public function __construct(Select $select, EntityWriter $entityWriter)
    {
        $this->entityWriter = $entityWriter;
        parent::__construct($select);
    }

    /**
     * Get items  without filter
     *
     * @psalm-return DataReaderInterface<int, Item>
     */
    public function findAllPreloaded(): DataReaderInterface
    {
        $query = $this->select();
        return $this->prepareDataReader($query);
    }
    
    /**
     * @psalm-return DataReaderInterface<int, Item>
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
    public function save(Item $item): void
    {
        $this->entityWriter->write([$item]);
    }
    
    /**
     * @throws Throwable
     */
    public function delete(Item $item): void
    {
        $this->entityWriter->delete([$item]);
    }
    
    private function prepareDataReader($query): EntityReader
    {
        return (new EntityReader($query))->withSort(
            Sort::only(['id'])
                ->withOrder(['id' => 'asc'])
        );
    }
    
    public function repoItemquery(string $id): Item    {
        $query = $this->select()->load('inv')
                                ->load('tax_rate')
                                ->load('product')
                                ->load('unit')
                                ->load('task')
                                ->where(['id' => $id]);
        return  $query->fetchOne();        
    }
}