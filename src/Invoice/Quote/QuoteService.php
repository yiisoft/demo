<?php

declare(strict_types=1); 

namespace App\Invoice\Quote;

use App\Invoice\Entity\Quote;
use App\User\User;
use App\Invoice\Quote\QuoteRepository;
use App\Invoice\Setting\SettingRepository;
use App\Invoice\Group\GroupRepository;


final class QuoteService
{

    private QuoteRepository $repository;

    public function __construct(QuoteRepository $repository)
    {
        $this->repository = $repository;
    }

    public function saveQuote(User $user,Quote $model, QuoteForm $form, SettingRepository $s): void
    { 
       $model->setInv_id($form->getInv_id());
       $model->setClient_id($form->getClient_id());
       $model->setGroup_id($form->getGroup_id());
       $model->setStatus_id($form->getStatus_id());
       $model->setDate_created($form->getDate_created());
       $model->setDate_expires($form->getDate_created(),$s);
       $model->setNumber($form->getNumber());
       $model->setDiscount_amount($form->getDiscount_amount());
       $model->setDiscount_percent($form->getDiscount_percent());
       $model->setUrl_key($form->getUrl_key());
       $model->setPassword($form->getPassword());
       $model->setNotes($form->getNotes());
       
       if ($model->isNewRecord()) {
            $model->setStatus_id(1);
            $model->setUser($user);
       }
        
       $this->repository->save($model);
    }
    
    public function deleteQuote(Quote $model): void
    {
        $this->repository->delete($model);
    }
}