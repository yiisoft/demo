<?php

declare(strict_types=1); 

namespace App\Invoice\InvAmount;

use App\Invoice\Entity\InvAmount;


final class InvAmountService
{

    private InvAmountRepository $repository;

    public function __construct(InvAmountRepository $repository)
    {
        $this->repository = $repository;
    }

    public function saveInvAmount(InvAmount $model, InvAmountForm $form): void
    {
       $model->setInv_id($form->getInv_id());
       $model->setSign($form->getSign());
       $model->setItem_sub_total($form->getItem_sub_total());
       $model->setItem_tax_total($form->getItem_tax_total());
       $model->setTax_total($form->getTax_total());
       $model->setInvoice_total($form->getInvoice_total());
       $model->setInvoice_paid($form->getInvoice_paid());
       $model->setInvoice_balance($form->getInvoice_balance()); 
       $this->repository->save($model);
    }
    
    public function deleteInvAmount(InvAmount $model): void
    {
        $this->repository->delete($model);
    }
}