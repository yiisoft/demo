<?php

declare(strict_types=1); 

namespace App\Invoice\ItemLookup;

use App\Invoice\Entity\ItemLookup;


final class ItemLookupService
{

    private ItemLookupRepository $repository;

    public function __construct(ItemLookupRepository $repository)
    {
        $this->repository = $repository;
    }

    public function saveItemLookup(ItemLookup $model, ItemLookupForm $form): void
    {
        
       $model->setName($form->getName());
       $model->setDescription($form->getDescription());
       $model->setPrice($form->getPrice());
 
        $this->repository->save($model);
    }
    
    public function deleteItemLookup(ItemLookup $model): void
    {
        $this->repository->delete($model);
    }
}