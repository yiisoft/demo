<?php

declare(strict_types=1); 

namespace App\Invoice\Item;

use App\Invoice\Entity\Item;

final class ItemService
{

    private ItemRepository $repository;

    public function __construct(ItemRepository $repository)
    {
        $this->repository = $repository;
    }

    public function saveItem(Item $model, ItemForm $form): void
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
    
    public function deleteItem(Item $model): void
    {
        $this->repository->delete($model);
    }
}