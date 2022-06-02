<?php

declare(strict_types=1); 

namespace App\Invoice\Profile;

use App\Invoice\Entity\Profile;
use Cycle\ORM\Select;
use Throwable;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Cycle\Data\Reader\EntityReader;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

final class ProfileRepository extends Select\Repository
{
private EntityWriter $entityWriter;

    public function __construct(Select $select, EntityWriter $entityWriter)
    {
        $this->entityWriter = $entityWriter;
        parent::__construct($select);
    }

    /**
     * Get profiles  without filter
     *
     * @psalm-return DataReaderInterface<int,Profile>
     */
    public function findAllPreloaded(): DataReaderInterface
    {
        $query = $this->select()->load('company');
        return $this->prepareDataReader($query);
    }
    
    /**
     * @psalm-return DataReaderInterface<int, Profile>
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
    public function save(Profile $profile): void
    {
        $this->entityWriter->write([$profile]);
    }
    
    /**
     * @throws Throwable
     */
    public function delete(Profile $profile): void
    {
        $this->entityWriter->delete([$profile]);
    }
    
    private function prepareDataReader($query): EntityReader
    {
        return (new EntityReader($query))->withSort(
            Sort::only(['id'])
                ->withOrder(['id' => 'asc'])
        );
    }
    
    public function repoProfilequery(string $id): Profile    {
        $query = $this->select()->load('company')->where(['id' =>$id]);
        return  $query->fetchOne();        
    }
}