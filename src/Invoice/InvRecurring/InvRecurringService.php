<?php

declare(strict_types=1); 

namespace App\Invoice\InvRecurring;

use App\Invoice\Entity\InvRecurring;


final class InvRecurringService
{

    private InvRecurringRepository $repository;

    public function __construct(InvRecurringRepository $repository)
    {
        $this->repository = $repository;
    }

    public function saveInvRecurring(InvRecurring $model, InvRecurringForm $form): void
    {
        
       $model->setInv_id($form->getInv_id());
       $model->setStart($form->getStart());
       $model->setEnd($form->getEnd());
       $model->setFrequency($form->getFrequency());
       $model->setNext($form->getNext());
 
        $this->repository->save($model);
    }
    
    public function deleteInvRecurring(InvRecurring $model): void
    {
        $this->repository->delete($model);
    }
}