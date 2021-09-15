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
        $query = $this->select();
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
    
    public function repoQuoteTaxRatequery(string $id): QuoteTaxRate    {
        $query = $this->select()->load('quote')->load('tax_rate')->where(['id' => $id]);
        return  $query->fetchOne();        
    }
}