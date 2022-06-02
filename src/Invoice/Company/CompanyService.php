<?php

declare(strict_types=1); 

namespace App\Invoice\Company;

use App\Invoice\Entity\Company;


final class CompanyService
{

    private CompanyRepository $repository;

    public function __construct(CompanyRepository $repository)
    {
        $this->repository = $repository;
    }

    public function saveCompany(Company $model, CompanyForm $form): void
    {
        
       $model->setCurrent($form->getCurrent());
       $model->setName($form->getName());
       $model->setAddress_1($form->getAddress_1());
       $model->setAddress_2($form->getAddress_2());
       $model->setCity($form->getCity());
       $model->setState($form->getState());
       $model->setZip($form->getZip());
       $model->setCountry($form->getCountry());
       $model->setPhone($form->getPhone());
       $model->setFax($form->getFax());
       $model->setEmail($form->getEmail());
       $model->setWeb($form->getWeb());       
 
       $this->repository->save($model);
    }
    
    public function deleteCompany(Company $model): void
    {
        $this->repository->delete($model);
    }
}