<?php

declare(strict_types=1); 

namespace App\Invoice\Sumex;

use App\Invoice\Entity\Sumex;


final class SumexService
{

    private SumexRepository $repository;

    public function __construct(SumexRepository $repository)
    {
        $this->repository = $repository;
    }

    public function saveSumex(Sumex $model, SumexForm $form): void
    {
        
       $model->setId($form->getId());
       $model->setInvoice($form->getInvoice());
       $model->setReason($form->getReason());
       $model->setDiagnosis($form->getDiagnosis());
       $model->setObservations($form->getObservations());
       $model->setTreatmentstart($form->getTreatmentstart());
       $model->setTreatmentend($form->getTreatmentend());
       $model->setCasedate($form->getCasedate());
       $model->setCasenumber($form->getCasenumber());
 
        $this->repository->save($model);
    }
    
    public function deleteSumex(Sumex $model): void
    {
        $this->repository->delete($model);
    }
}