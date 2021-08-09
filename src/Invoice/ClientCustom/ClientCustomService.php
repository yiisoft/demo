<?php

declare(strict_types=1); 

namespace App\Invoice\ClientCustom;

use App\Invoice\Entity\ClientCustom;


final class ClientCustomService
{

    private ClientCustomRepository $repository;

    public function __construct(ClientCustomRepository $repository)
    {
        $this->repository = $repository;
    }

    public function saveClientCustom(ClientCustom $model, ClientCustomForm $form): void
    {
        
       $model->setClient_id($form->getClient_id());
       $model->setFieldid($form->getFieldid());
       $model->setFieldvalue($form->getFieldvalue());
 
        $this->repository->save($model);
    }
    
    public function deleteClientCustom(ClientCustom $model): void
    {
        $this->repository->delete($model);
    }
}