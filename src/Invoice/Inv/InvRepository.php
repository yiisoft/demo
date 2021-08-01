<?php

declare(strict_types=1); 

namespace App\Invoice\Inv;

use App\Invoice\Entity\Inv;
use Cycle\ORM\Select;
use Throwable;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Cycle\Data\Reader\EntityReader;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

final class InvRepository extends Select\Repository
{
    private EntityWriter $entityWriter;

    public function __construct(Select $select, EntityWriter $entityWriter)
    {
        $this->entityWriter = $entityWriter;
        parent::__construct($select);
    }

    /**
     * Get invs  without filter
     *
     * @psalm-return DataReaderInterface<int, Inv>
     */
    public function findAllPreloaded(): DataReaderInterface
    {
        $query = $this->select();
        return $this->prepareDataReader($query);
    }
    
    /**
     * @psalm-return DataReaderInterface<int, Inv>
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
    public function save(Inv $inv): void
    {
        $this->entityWriter->write([$inv]);
    }
    
    /**
     * @throws Throwable
     */
    public function delete(Inv $inv): void
    {
        $this->entityWriter->delete([$inv]);
    }
    
    private function prepareDataReader($query): EntityReader
    {
        return (new EntityReader($query))->withSort(
            Sort::only(['id'])
                ->withOrder(['id' => 'asc'])
        );
    }
    
    public function repoInvquery(string $id): Inv    {
        $query = $this
                ->select()
                ->load('user')
                ->load('group')
                ->load('client')
                ->where(['id' => $id]);
        return  $query->fetchOne();        
    }
}