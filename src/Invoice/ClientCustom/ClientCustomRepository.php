<?php

declare(strict_types=1); 

namespace App\Invoice\ClientCustom;

use App\Invoice\Entity\ClientCustom;
use Cycle\ORM\Select;
use Throwable;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Cycle\Data\Reader\EntityReader;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

final class ClientCustomRepository extends Select\Repository
{
private EntityWriter $entityWriter;

    public function __construct(Select $select, EntityWriter $entityWriter)
    {
        $this->entityWriter = $entityWriter;
        parent::__construct($select);
    }

    /**
     * Get clientcustoms  without filter
     *
     * @psalm-return DataReaderInterface<int,ClientCustom>
     */
    public function findAllPreloaded(): DataReaderInterface
    {
        $query = $this->select()
                ->load('client')
                ->load('custom_field');
        return $this->prepareDataReader($query);
    }
    
    /**
     * @psalm-return DataReaderInterface<int, ClientCustom>
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
    public function save(ClientCustom $clientcustom): void
    {
        $this->entityWriter->write([$clientcustom]);
    }
    
    /**
     * @throws Throwable
     */
    public function delete(ClientCustom $clientcustom): void
    {
        $this->entityWriter->delete([$clientcustom]);
    }
    
    private function prepareDataReader($query): EntityReader
    {
        return (new EntityReader($query))->withSort(
            Sort::only(['id'])
                ->withOrder(['id' => 'asc'])
        );
    }
   
    public function repoClientCustomquery(string $id): ClientCustom    {
        $query = $this->select()->load('client')
        ->load('custom_field')
        ->where(['id' =>$id]);
        return  $query->fetchOne();        
    }
    
    public function repoClientCount(string $client_id) : int {
        $query = $this->select()->where(['client_id' =>$client_id]);
        return $query->count();
    }
    
    public function repoFormValuequery(string $client_id, string $custom_field_id): ClientCustom {
        $query = $this->select()->where(['client_id' =>$client_id])
                                ->andWhere(['custom_field_id' =>$custom_field_id]);
        return  $query->fetchOne();        
    }
    
    public function repoClientCustomCount(string $client_id, string $custom_field_id) : int {
        $query = $this->select()->where(['client_id' =>$client_id])
                                ->andWhere(['custom_field_id' =>$custom_field_id]);
        return $query->count();
    } 
    
    /**
     * Get all fields that have been setup for a particular client
     *
     * @psalm-return DataReaderInterface<int,ClientCustom>
     */
    public function repoFields(string $client_id): DataReaderInterface
    {
        $query = $this->select()->where(['client_id'=>$client_id]);                
        return $this->prepareDataReader($query);
    }
}