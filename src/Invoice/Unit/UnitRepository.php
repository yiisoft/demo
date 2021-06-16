<?php

declare(strict_types=1);

namespace App\Invoice\Unit;

use App\Invoice\Entity\Unit;
use Cycle\ORM\Select;
use Throwable;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Cycle\Data\Reader\EntityReader;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

final class UnitRepository extends Select\Repository
{
    private EntityWriter $entityWriter;
    
    public function __construct(Select $select, EntityWriter $entityWriter)
    {
        $this->entityWriter = $entityWriter;
        parent::__construct($select);
    }
    
    /**
     * Get units without filter
     *
     * @psalm-return DataReaderInterface<int, Unit>
     */
    public function findAllPreloaded(): DataReaderInterface
    {
        $query = $this->select();
        return $this->prepareDataReader($query);
    }
            
    /**
     * @throws Throwable
     */
    public function save(Unit $unit): void
    {
        $this->entityWriter->write([$unit]);
    }
    
    /**
     * @throws Throwable
     */
    public function delete(Unit $unit): void
    {
        $this->entityWriter->delete([$unit]);
    }

    private function prepareDataReader($query): EntityReader
    {
        return (new EntityReader($query))->withSort(
            Sort::only(['id', 'unit_name', 'unit_name_plrl'])
                ->withOrder(['unit_name' => 'asc'])
        );
    }
    
    public function repoUnitquery(string $unit_id): Unit
    {
        $query = $this
            ->select()
            ->where(['id' => $unit_id]);
        return  $query->fetchOne();        
    }
    
    public function withName(string $unit_name): ?Unit
    {
        $query = $this
            ->select()
            ->where(['unit_name' => $unit_name]);
        return  $query->fetchOne();
    }
}
