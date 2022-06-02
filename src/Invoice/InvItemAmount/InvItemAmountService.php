<?php

declare(strict_types=1); 

namespace App\Invoice\InvItemAmount;

use App\Invoice\Entity\InvItemAmount;


final class InvItemAmountService
{
    private InvItemAmountRepository $repository;    

    public function __construct(InvItemAmountRepository $repository)
    {
        $this->repository = $repository;
    }

    public function saveInvItemAmount(InvItemAmount $model, InvItemAmountForm $form): void
    { 
       $model->setInv_item_id($form->getInv_item_id());
       $model->setSubtotal($form->getSubtotal());
       $model->setTax_total($form->getTax_total());
       $model->setDiscount($form->getDiscount());
       $model->setTotal($form->getTotal());
       $this->repository->save($model);
    }
    
    public function saveInvItemAmountNoForm(InvItemAmount $model, $invitem): void
    {        
       $model->setInv_item_id((int)$invitem['inv_item_id']);
       $model->setSubtotal($invitem['subtotal']);
       $model->setTax_total($invitem['taxtotal']);
       $model->setDiscount($invitem['discount']);
       $model->setTotal($invitem['total']); 
       $this->repository->save($model);
    }
    
    public function deleteInvItemAmount(InvItemAmount $model): void
    {
       $this->repository->delete($model);
    }
}