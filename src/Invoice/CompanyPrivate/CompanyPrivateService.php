<?php

declare(strict_types=1); 

namespace App\Invoice\CompanyPrivate;

use App\Invoice\Entity\CompanyPrivate;


final class CompanyPrivateService
{

    private CompanyPrivateRepository $repository;

    public function __construct(CompanyPrivateRepository $repository)
    {
        $this->repository = $repository;
    }

    public function saveCompanyPrivate(CompanyPrivate $model, CompanyPrivateForm $form): void
    {
        
       $model->setCompany_id($form->getCompany_id());
       $model->setVat_id($form->getVat_id());
       $model->setTax_code($form->getTax_code());
       $model->setIban($form->getIban());
       $model->setGln($form->getGln());
       $model->setRcc($form->getRcc());
 
        $this->repository->save($model);
    }
    
    public function deleteCompanyPrivate(CompanyPrivate $model): void
    {
        $this->repository->delete($model);
    }
}