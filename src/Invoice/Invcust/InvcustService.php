<?php

declare(strict_types=1); 

namespace App\Invoice\Invcust;

use App\Invoice\Entity\Invcust;


final class InvcustService
{
    private InvcustRepository $repository;

    public function __construct(InvcustRepository $repository)
    {
        $this->repository = $repository;
    }

    public function saveInvcust(Invcust $model, InvcustForm $form): void
    {
       $model->setInv_id($form->getInv_id());
       $model->setFieldid($form->getFieldid());
       $model->setFieldvalue($form->getFieldvalue());
       $this->repository->save($model);
    }
    
    public function deleteInvcust(Invcust $model): void
    {
        $this->repository->delete($model);
    }
}