<?php

declare(strict_types=1); 

namespace App\Invoice\CustomValue;

use App\Invoice\Entity\CustomValue;
use Cycle\ORM\Select;
use Throwable;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Cycle\Data\Reader\EntityReader;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

final class CustomValueRepository extends Select\Repository
{
private EntityWriter $entityWriter;

    public function __construct(Select $select, EntityWriter $entityWriter)
    {
        $this->entityWriter = $entityWriter;
        parent::__construct($select);
    }

    /**
     * Get customvalues  without filter
     *
     * @psalm-return DataReaderInterface<int,CustomValue>
     */
    public function findAllPreloaded(): DataReaderInterface
    {
        $query = $this->select();
        return $this->prepareDataReader($query);
    }
    
    /**
     * @psalm-return DataReaderInterface<int, CustomValue>
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
    public function save(CustomValue $customvalue): void
    {
        $this->entityWriter->write([$customvalue]);
    }
    
    /**
     * @throws Throwable
     */
    public function delete(CustomValue $customvalue): void
    {
        $this->entityWriter->delete([$customvalue]);
    }
    
    private function prepareDataReader($query): EntityReader
    {
        return (new EntityReader($query))->withSort(
            Sort::only(['id'])
                ->withOrder(['id' => 'asc'])
        );
    }
    
    public function repoCustomValuequery(string $id): CustomValue {
        $query = $this->select()->where(['id' =>$id]);
        return  $query->fetchOne();        
    }
    
    public function repoCount($id): int {
        $count = $this->select()
                      ->where(['id' => $id])
                      ->count();
        return $count;   
    }
        
    /**
     * Get customvalues  with filter
     *
     * @psalm-return DataReaderInterface<int,CustomValue>
     */
    public function repoCustomFieldquery(int $custom_field_id): DataReaderInterface    {
        $query = $this->select()->where(['custom_field_id' =>$custom_field_id]);
        return $this->prepareDataReader($query);        
    }
    
    public function attach_hard_coded_custom_field_values_to_custom_field($custom_fields) : array{
        $custom_values  = [];
        foreach ($custom_fields as $custom_field) {
            if (in_array($custom_field->getType(),['SINGLE-CHOICE','MULTIPLE-CHOICE'])) {
                // build the $custom_values array with the eg. dropdown values for the field whether it be a multiple-choice field or a single-choice field
                $custom_values[$custom_field->getId()] = $this->repoCustomFieldquery((integer)$custom_field->getId());
            }
        }
        return $custom_values;
    }
    
}