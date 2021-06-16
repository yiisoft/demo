<?php

declare(strict_types=1);

namespace App\Invoice\Family;

use App\Invoice\Entity\Family;
use Cycle\ORM\Select;
use Throwable;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Cycle\Data\Reader\EntityReader;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

final class FamilyRepository extends Select\Repository
{
    private EntityWriter $entityWriter;
    
    public function __construct(Select $select, EntityWriter $entityWriter)
    {
        $this->entityWriter = $entityWriter;
        parent::__construct($select);
    }
    
    /**
     * Get families without filter
     *
     * @psalm-return DataReaderInterface<int, Family>
     */
    public function findAllPreloaded(): DataReaderInterface
    {
        $query = $this->select();
        return $this->prepareDataReader($query);
    }
            
    /**
     * @throws Throwable
     */
    public function save(Family $family): void
    {
        $this->entityWriter->write([$family]);
    }
    
    /**
     * @throws Throwable
     */
    public function delete(Family $family): void
    {
        $this->entityWriter->delete([$family]);
    }

    private function prepareDataReader($query): EntityReader
    {
        return (new EntityReader($query))->withSort(
            Sort::only(['id', 'family_name'])
                ->withOrder(['family_name' => 'asc'])
        );
    }
    
    public function repoFamilyquery(string $family_id): Family
    {
        $query = $this
            ->select()
            ->where(['id' => $family_id]);
        return  $query->fetchOne();        
    }
    
    public function withName(string $family_name): ?Family
    {
        $query = $this
            ->select()
            ->where(['family_name' => $family_name]);
        return  $query->fetchOne();
    }
}
