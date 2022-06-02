<?php

declare(strict_types=1); 

namespace App\Invoice\UserClient;

use App\Invoice\Entity\UserClient;
use App\Invoice\Entity\Client;
use App\User\User;


final class UserClientService
{

    private UserClientRepository $repository;

    public function __construct(UserClientRepository $repository)
    {
        $this->repository = $repository;
    }

    public function saveUserClient(UserClient $model, $form): void
    {        
       $model->setUser_id($form->getUser_id());
       $model->setClient_id($form->getClient_id());
       $this->repository->save($model);
    }
    
    public function deleteUserClient(UserClient $model): void
    {       
       $this->repository->delete($model);
    }
}