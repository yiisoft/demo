<?php

declare(strict_types=1); 

namespace App\Invoice\Merchant;

use App\Invoice\Entity\Merchant;


final class MerchantService
{

    private MerchantRepository $repository;

    public function __construct(MerchantRepository $repository)
    {
        $this->repository = $repository;
    }

    public function saveMerchant(Merchant $model, MerchantForm $form): void
    {
       $model->setInv_id($form->getInv_id());
       $model->setSuccessful($form->getSuccessful());
       $model->setDate($form->getDate());
       $model->setDriver($form->getDriver());
       $model->setResponse($form->getResponse());
       $model->setReference($form->getReference());
 
       $this->repository->save($model);
    }
    
    public function deleteMerchant(Merchant $model): void
    {
        $this->repository->delete($model);
    }
}