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
    
    public function initializeInvAmount(InvAmount $model, $inv_id) : void
    {
       $model->setInv_id((int)$inv_id);
       $model->setSign('1');
       $model->setItem_subtotal(0.00);
       $model->setItem_tax_total(0.00);
       $model->setTax_total(0.00);
       $model->setTotal(0.00); 
       $model->setPaid(0.00);
       $model->setBalance(0.00); 
       $this->repository->save($model);
    }

    public function initializeCreditInvAmount(InvAmount $model, $basis_inv_id, $new_inv_id) : void
    {
       $basis_invoice = $this->repository->repoInvquery($basis_inv_id);
       $model->setInv_id((int)$new_inv_id);
       $model->setSign('-1');
       $model->setItem_subtotal($basis_invoice->getItem_subtotal()*-1);
       $model->setItem_tax_total($basis_invoice->getItem_tax_total()*-1);
       $model->setTax_total($basis_invoice->getTax_total()*-1);
       $model->setTotal($basis_invoice->getTotal()*-1); 
       $model->setPaid(0.00);
       $model->setBalance($basis_invoice->getBalance()*-1); 
       $this->repository->save($model);
    }

    public function initializeCopyInvAmount(InvAmount $model, $basis_inv_id, $new_inv_id) : void
    {
       $basis_invoice = $this->repository->repoInvquery($basis_inv_id);
       $model->setInv_id((int)$new_inv_id);
       $model->setSign('1');
       $model->setItem_subtotal($basis_invoice->getItem_subtotal());
       $model->setItem_tax_total($basis_invoice->getItem_tax_total());
       $model->setTax_total($basis_invoice->getTax_total());
       $model->setTotal($basis_invoice->getTotal()); 
       $model->setPaid(0.00);
       $model->setBalance($basis_invoice->getBalance()); 
       $this->repository->save($model);
    } 

    public function saveInvAmount(InvAmount $model, InvAmountForm $form): void
    {        
       $model->setInv_id($form->getInv_id());
       $model->setItem_subtotal($form->getItem_subtotal());
       $model->setItem_tax_total($form->getItem_tax_total());
       $model->setTax_total($form->getTax_total());
       $model->setTotal($form->getTotal()); 
       $model->setPaid($form->getPaid());
       $model->setBalance($form->getBalance()); 
       $this->repository->save($model);
    }
    
    public function saveInvAmountViaCalculations(InvAmount $model, $array): void
    {        
       $model->setInv_id($array['inv_id']);
       $model->setItem_subtotal($array['item_subtotal']);
       $model->setItem_tax_total($array['item_taxtotal']);
       $model->setTax_total($array['tax_total']);
       $model->setTotal($array['total']);
       $model->setPaid($array['paid']);
       $model->setBalance($array['balance']);
       $this->repository->save($model);
    }
    
    public function deleteInvAmount(InvAmount $model): void
    {
        $this->repository->delete($model);
    }
}