<?php

declare(strict_types=1); 

namespace App\Invoice\InvTaxRate;

use App\Invoice\Entity\InvTaxRate;


final class InvTaxRateService
{

    private InvTaxRateRepository $repository;

    public function __construct(InvTaxRateRepository $repository)
    {
        $this->repository = $repository;
    }

    public function saveInvTaxRate(InvTaxRate $model, InvTaxRateForm $form): void
    {
        
       $model->setInv_id($form->getInv_id());
       $model->setTax_rate_id($form->getTax_rate_id());
       $model->setInclude_item_tax($form->getInclude_item_tax());
       $model->setAmount($form->getAmount());
 
        $this->repository->save($model);
    }
    
    public function deleteInvTaxRate(InvTaxRate $model): void
    {
        $this->repository->delete($model);
    }
}