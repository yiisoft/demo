<?php

declare(strict_types=1); 

namespace App\Invoice\CompanyPrivate;

use App\Invoice\Entity\CompanyPrivate;
use Cycle\ORM\Select;
use Throwable;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Cycle\Data\Reader\EntityReader;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

final class CompanyPrivateRepository extends Select\Repository
{
private EntityWriter $entityWriter;

    public function __construct(Select $select, EntityWriter $entityWriter)
    {
        $this->entityWriter = $entityWriter;
        parent::__construct($select);
    }

    /**
     * Get companyprivates  without filter
     *
     * @psalm-return DataReaderInterface<int,CompanyPrivate>
     */
    public function findAllPreloaded(): DataReaderInterface
    {
        $query = $this->select()->load('company');
        return $this->prepareDataReader($query);
    }
    
    /**
     * @psalm-return DataReaderInterface<int, CompanyPrivate>
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
    public function save(CompanyPrivate $companyprivate): void
    {
        $this->entityWriter->write([$companyprivate]);
    }
    
    /**
     * @throws Throwable
     */
    public function delete(CompanyPrivate $companyprivate): void
    {
        $this->entityWriter->delete([$companyprivate]);
    }
    
    private function prepareDataReader($query): EntityReader
    {
        return (new EntityReader($query))->withSort(
            Sort::only(['id'])
                ->withOrder(['id' => 'asc'])
        );
    }
    
    public function repoCompanyPrivatequery(string $id): CompanyPrivate    {
        $query = $this->select()->load('company')->where(['id' =>$id]);
        return  $query->fetchOne();        
    }
}