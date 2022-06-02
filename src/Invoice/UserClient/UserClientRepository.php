<?php

declare(strict_types=1); 

namespace App\Invoice\UserClient;

use App\Invoice\Entity\UserClient;
use App\Invoice\UserClient\UserClientForm;
use App\Invoice\UserClient\UserClientService as UCS;
use App\Invoice\UserClient\UserClientRepository as UCR;
use App\Invoice\UserInv\UserInvRepository as UIR;
use App\Invoice\Client\ClientRepository as CR;

use Cycle\ORM\Select;
use Throwable;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\Cycle\Data\Reader\EntityReader;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

final class UserClientRepository extends Select\Repository
{
private EntityWriter $entityWriter;

    public function __construct(Select $select, EntityWriter $entityWriter)
    {
        $this->entityWriter = $entityWriter;
        parent::__construct($select);
    }

    /**
     * Get userclients  without filter
     *
     * @psalm-return DataReaderInterface<int,UserClient>
     */
    public function findAllPreloaded(): DataReaderInterface
    {
        $query = $this->select();
        return $this->prepareDataReader($query);
    }
    
    /**
     * @psalm-return DataReaderInterface<int, UserClient>
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
    public function save(UserClient $userclient): void
    {
        $this->entityWriter->write([$userclient]);
    }
    
    /**
     * @throws Throwable
     */
    public function delete(UserClient $userclient): void
    {
        $this->entityWriter->delete([$userclient]);
    }
    
    private function prepareDataReader($query): EntityReader
    {
        return (new EntityReader($query))->withSort(
            Sort::only(['id'])
                ->withOrder(['id' => 'asc'])
        );
    }
    
    public function repoUserClientquery(string $id): UserClient    {
        $query = $this->select()->load('user')->load('client')->where(['id' => $id]);
        return  $query->fetchOne();        
    }
        
     /**
     * Get clients  with filter user_id
     *
     * @psalm-return DataReaderInterface<int,UserClient>
     */
    public function repoClientquery(string $user_id): DataReaderInterface    {
        $query = $this->select()->load('client')->where(['user_id' => $user_id]);
        return $this->prepareDataReader($query);     
    }
    
    public function repoClientCountquery(string $user_id): int {
        $query = $this->select()
                      ->where(['user_id' => $user_id]);                      
        return $query->count();     
    }
    
    public function repoCheckNotExistClientIdquery(string $client_id): int {
        $query = $this->select()
                      ->where(['client_id' => $client_id]);                      
        return $query->count();     
    }
    
    /**
     * @param $user_id
     * @return $this
     */
    
    // Return client ids that are ACTIVE and have NOT been assigned to the user  and are therefore available to be assigned
    public function get_not_assigned_to_user($user_id, CR $cR) : array
    {
        // Get all clients assigned to this user
        $count_clients = $this->repoClientCountquery($user_id);        

        $assigned_client_ids = [];
        if ($count_clients>0) {
            $clients = $this->repoClientquery($user_id);        
            foreach ($clients as $client) {
                $client->getClient()->getClient_active() ? $assigned_client_ids[] = $client->getClient_id() : '';                 
            }
        }
        
        $all_clients = $cR->findAllPreloaded();
        $every_client_ids = [];
        foreach ($all_clients as $client) {
            $client_id = $client->getClient_id();
            $every_client_ids[] = $client_id;
        }
        
        $possible_client_ids = array_diff($every_client_ids,$assigned_client_ids);
        
        return $possible_client_ids;
    }
    
    public function reset_users_all_clients(UIR $uiR, CR $cR, UCS $ucS, ValidatorInterface $validator) : void
    {
        // Users that have their all_clients setting active
        if ($uiR->countAllWithAllClients()>0) {
            $users = $uiR->findAllWithAllClients();
            foreach ($users as $user) {
                $user_id = $user->getUser_id();
                $available_client_ids = $this->get_not_assigned_to_user($user_id, $cR); 
                $this->assign_to_user_client($available_client_ids, $user_id, $validator, $ucS);
            }
        }            
    }
    
    public function assign_to_user_client($available_client_ids, $user_id, $validator, $ucS){
        foreach ($available_client_ids as $key => $value) {
                   $user_client = [
                        'user_id' => $user_id,
                        'client_id' => $value,
                    ]; 
                    $form = new UserClientForm();
                    ($form->load($user_client) && $validator->validate($form)->isValid()) ? $ucS->saveUserClient(new UserClient(), $form) : '';
        }
    }
    
    public function unassign_to_user_client($user_id) : void {
        $clients = $this->repoClientquery($user_id);        
        foreach ($clients as $client) {
            $this->delete($client);
        }
    }
}