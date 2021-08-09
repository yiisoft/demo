<?php

declare(strict_types=1); 

namespace App\Invoice\ClientNote;

use App\Invoice\Entity\ClientNote;


final class ClientNoteService
{

    private ClientNoteRepository $repository;

    public function __construct(ClientNoteRepository $repository)
    {
        $this->repository = $repository;
    }

    public function saveClientNote(ClientNote $model, ClientNoteForm $form): void
    {
        
       $model->setClient_id($form->getClient_id());
       $model->setDate($form->getDate());
       $model->setNote($form->getNote());
 
        $this->repository->save($model);
    }
    
    public function deleteClientNote(ClientNote $model): void
    {
        $this->repository->delete($model);
    }
}