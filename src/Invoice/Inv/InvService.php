<?php

declare(strict_types=1); 

namespace App\Invoice\Inv;

use App\Invoice\Entity\Inv;
use App\User\User;
use App\Invoice\Inv\InvRepository;
use App\Invoice\Setting\SettingRepository;


final class InvService
{

    private InvRepository $repository;

    public function __construct(InvRepository $repository)
    {
        $this->repository = $repository;
    }

    public function saveInv(User $user, Inv $model, InvForm $form, SettingRepository $s): void
    { 
       $model->setClient_id($form->getClient_id());
       $model->setGroup_id($form->getGroup_id());
       $model->setPassword($form->getPassword());
       $model->setDate_created($form->getDate_created());
       $model->setDate_due($form->getDate_created(),$s);
       $model->setTime_created($form->getTime_created());
       $model->setTerms($form->getTerms());
       $model->setPayment_method($form->getPayment_method());
       
       if ($model->isNewRecord()) {
            $model->setStatus_id(1);
            $model->setIs_read_only(false);
            $model->setUser($user);
        }
       
        $this->repository->save($model);
    }
    
    public function deleteInv(Inv $model): void
    {
       $this->repository->delete($model);
    }
}