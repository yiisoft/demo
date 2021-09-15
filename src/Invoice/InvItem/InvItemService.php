<?php

declare(strict_types=1); 

namespace App\Invoice\InvItem;

use App\Invoice\Entity\InvItem;

final class InvItemService
{

    private InvItemRepository $repository;

    public function __construct(InvItemRepository $repository)
    {
        $this->repository = $repository;
    }

    public function saveInvItem(InvItem $model, InvItemForm $form): void
    {
       $model->setInv_id($form->getInv_id());
       $model->setTax_rate_id($form->getTax_rate_id());
       $model->setProduct_id($form->getProduct_id());       
       $model->setUnit_id($form->getUnit_id());
       $model->setTask_id($form->getTask_id());
       $model->setDate_added($form->getDate_added());
       $model->setName($form->getName());
       $model->setDescription($form->getDescription());
       $model->setQuantity($form->getQuantity());
       $model->setPrice($form->getPrice());
       $model->setDiscount_amount($form->getDiscount_amount());
       $model->setOrder($form->getOrder());
       $model->setIs_recurring($form->getIs_recurring());
       $model->setProduct_unit($form->getProduct_unit());
       $model->setDate($form->getDate());
 
       $this->repository->save($model);
    }
    
    public function deleteInvItem(InvItem $model): void
    {
        $this->repository->delete($model);
    }
}