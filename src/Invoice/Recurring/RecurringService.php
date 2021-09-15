<?php

declare(strict_types=1); 

namespace App\Invoice\Recurring;

use App\Invoice\Entity\Recurring;


final class RecurringService
{

    private RecurringRepository $repository;

    public function __construct(RecurringRepository $repository)
    {
        $this->repository = $repository;
    }

    public function saveRecurring(Recurring $model, RecurringForm $form): void
    {
        
       $model->setStart_date($form->getStart_date());
       $model->setEnd_date($form->getEnd_date());
       $model->setFrequency($form->getFrequency());
       $model->setNext_date($form->getNext_date());
       $model->setInv_id($form->getInv_id());
 
        $this->repository->save($model);
    }
    
    public function deleteRecurring(Recurring $model): void
    {
        $this->repository->delete($model);
    }
}