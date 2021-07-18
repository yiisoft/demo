<?php

declare(strict_types=1);

namespace App\Invoice\GeneratorRelation;

use App\Invoice\Entity\GentorRelation;
use Cycle\ORM\Select;
use Throwable;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Cycle\Data\Reader\EntityReader;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

final class GeneratorRelationRepository extends Select\Repository
{
    private EntityWriter $entityWriter;
    
    public function __construct(Select $select, EntityWriter $entityWriter)
    {
        $this->entityWriter = $entityWriter;
        parent::__construct($select);
    }
    
    /**
     * Get generatorrelations without filter
     *
     * @psalm-return DataReaderInterface<int, GentorRelation>
     */
    public function findAllPreloaded(): DataReaderInterface
    {
        $query = $this->select();
        return $this->prepareDataReader($query);
    }
    
        
    public function findRelations(string $id): DataReaderInterface 
    {
        $query = $this->select()->load('gentor')->where('gentor_id',$id);
        return $this->prepareDataReader($query);
    }
    
    /**
     * @throws Throwable
     */
    public function save(GentorRelation $generatorrelation): void
    {
        $this->entityWriter->write([$generatorrelation]);
    }
    
    /**
     * @throws Throwable
     */
    public function delete(GentorRelation $generatorrelation): void
    {
        $this->entityWriter->delete([$generatorrelation]);
    }

    private function prepareDataReader($query): EntityReader
    {
        return (new EntityReader($query))->withSort(
            Sort::only(['lowercasename','camelcasename','gentor_id'])
                ->withOrder(['gentor_id' => 'asc'])
        );
    }
    
    public function repoGeneratorRelationquery(string $id): GentorRelation
    {
        $query = $this
            ->select()
            ->load('gentor')
            ->where(['id' => $id]);
        return  $query->fetchOne();        
    }
    
    public function withLowercaseName(string $generatorrelation_lowercase_name): ?GentorRelation
    {
        $query = $this
            ->select()
            ->where(['lowercasename' => $generatorrelation_lowercase_name]);
        return  $query->fetchOne();
    }
}
