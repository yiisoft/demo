<?php

declare(strict_types=1); 

namespace App\Invoice\InvTaxRate;

use App\Invoice\Entity\InvTaxRate;
use Cycle\ORM\Select;
use Throwable;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Cycle\Data\Reader\EntityReader;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

final class InvTaxRateRepository extends Select\Repository
{
private EntityWriter $entityWriter;

    public function __construct(Select $select, EntityWriter $entityWriter)
    {
        $this->entityWriter = $entityWriter;
        parent::__construct($select);
    }

    /**
     * Get invtaxrates  without filter
     *
     * @psalm-return DataReaderInterface<int,InvTaxRate>
     */
    public function findAllPreloaded(): DataReaderInterface
    {
        $query = $this->select();
        return $this->prepareDataReader($query);
    }
    
    /**
     * @psalm-return DataReaderInterface<int, InvTaxRate>
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
    public function save(InvTaxRate $invtaxrate): void
    {
        $this->entityWriter->write([$invtaxrate]);
    }
    
    /**
     * @throws Throwable
     */
    public function delete(InvTaxRate $invtaxrate): void
    {
        $this->entityWriter->delete([$invtaxrate]);
    }
    
    private function prepareDataReader($query): EntityReader
    {
        return (new EntityReader($query))->withSort(
            Sort::only(['id'])
                ->withOrder(['id' => 'asc'])
        );
    }
    
    public function repoInvTaxRatequery(string $id): InvTaxRate    {
        $query = $this->select()->load('inv')->load('tax_rate')->where(['id' => $id]);
        return  $query->fetchOne();        
    }
}