<?php

declare(strict_types=1); 

namespace App\Invoice\CustomField;

use App\Invoice\Entity\CustomField;


final class CustomFieldService
{

    private CustomFieldRepository $repository;

    public function __construct(CustomFieldRepository $repository)
    {
        $this->repository = $repository;
    }

    public function saveCustomField(CustomField $model, CustomFieldForm $form): void
    {
        
       $model->setTable($form->getTable());
       $model->setLabel($form->getLabel());
       $model->setType($form->getType());
       $model->setLocation($form->getLocation());
       $model->setOrder($form->getOrder());
 
        $this->repository->save($model);
    }
    
    public function deleteCustomField(CustomField $model): void
    {
        $this->repository->delete($model);
    }
}