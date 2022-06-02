<?php

declare(strict_types=1); 

namespace App\Invoice\CustomValue;

use App\Invoice\Entity\CustomValue;


final class CustomValueService
{

    private CustomValueRepository $repository;

    public function __construct(CustomValueRepository $repository)
    {
        $this->repository = $repository;
    }

    public function saveCustomValue(CustomValue $model, CustomValueForm $form): void
    { 
       $model->setCustom_field_id($form->getCustom_field_id());
       $model->setValue($form->getValue());
       $this->repository->save($model);
    }
    
    public function deleteCustomValue(CustomValue $model): void
    {
       $this->repository->delete($model);
    }
}