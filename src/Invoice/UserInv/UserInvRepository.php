<?php

declare(strict_types=1); 

namespace App\Invoice\UserInv;

use App\Invoice\Entity\UserInv;
use Cycle\ORM\Select;
use Throwable;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Cycle\Data\Reader\EntityReader;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

final class UserInvRepository extends Select\Repository
{
private EntityWriter $entityWriter;

    public function __construct(Select $select, EntityWriter $entityWriter)
    {
        $this->entityWriter = $entityWriter;
        parent::__construct($select);
    }

    /**
     * Get userinvs  without filter
     *
     * @psalm-return DataReaderInterface<int,UserInv>
     */
    public function findAllPreloaded(): DataReaderInterface
    {
        $query = $this->select(); 
        return $this->prepareDataReader($query);
    }
    
    /**
     * @psalm-return DataReaderInterface<int, UserInv>
     */
    public function getReader(): DataReaderInterface
    {
        return (new EntityReader($this->select()))
            ->withSort($this->getSort());
    }
    
    private function getSort(): Sort
    {
        return Sort::only(['user_id','name','email'])->withOrder(['user_id' => 'asc']);
    }
    
    /**
     * @throws Throwable
     */
    public function save(UserInv $userinv): void
    {
        $this->entityWriter->write([$userinv]);
    }
    
    /**
     * @throws Throwable
     */
    public function delete(UserInv $userinv): void
    {
        $this->entityWriter->delete([$userinv]);
    }
    
    private function prepareDataReader($query): EntityReader
    {
        return (new EntityReader($query))->withSort(
            Sort::only(['user_id','name','email'])
                ->withOrder(['user_id' => 'asc'])
        );
    }
    
    public function repoUserInvquery(string $id): UserInv    {
        $query = $this->select()->where(['id'=>$id]);
        return  $query->fetchOne();        
    }
    
    public function repoUserInvcount(string $id): int {
        $query = $this->select()->where(['id' =>$id]);
        return $query->count();
    }
    
    public function repoUserInvUserIdquery(string $user_id): UserInv    {
        $query = $this->select()->where(['user_id'=>$user_id]);
        return  $query->fetchOne();        
    }
    
    public function repoUserInvUserIdcount(string $user_id): int {
        $query = $this->select()->where(['id' =>$user_id]);
        return $query->count();
    }

    /**
     * Get Userinv with filter active
     *
     * @psalm-return DataReaderInterface<int, UserInv>
     */
    public function findAllWithActive($active) : DataReaderInterface
    {
        if (($active) < 2) {
         $query = $this->select()
                ->where(['active' => $active]);  
         return $this->prepareDataReader($query);
       } else {
         return $this->findAllPreloaded();  
       }       
    }

    /**
     * Get Userinv with filter all_clients
     *
     * @psalm-return DataReaderInterface<int, UserInv>
     */
    
    // Find users that have access to all clients
    public function findAllWithAllClients() : DataReaderInterface
    {
        $query = $this->select()
                ->where(['all_clients' => 1]);  
        return $this->prepareDataReader($query);              
    }
    
    // Find users that have access to all clients
    public function countAllWithAllClients() : int
    {
        $query = $this->select()
                ->where(['all_clients' => 1]);  
        return $query->count();              
    }   
}