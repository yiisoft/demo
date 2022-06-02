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
    
    public function repoCount($unit_id): int {
        $count = $this->select()
                      ->where(['id' => $unit_id])
                      ->count();
        return $count;   
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
    
    /**
     * Return either the singular unit name or the plural unit name,
     * depending on the quantity
     *
     * @param $unit_id
     * @param $quantity
     * @return mixed
     */
    public function singular_or_plural_name($unit_id, $quantity)
    {
        if ((int)$unit_id === 0) { return '';} else {
            $unit = $this->repoUnitquery($unit_id);
            if ($quantity == -1 || $quantity == 1) {
                return $unit->getUnit_name();
            } else {
                return $unit->getUnit_name_plrl();
            }        
        }
    }
}
