<?php

declare(strict_types=1); 

namespace App\Invoice\UserInv;

use App\Invoice\Entity\UserInv;

final class UserInvService
{
    private UserInvRepository $repository;

    public function __construct(UserInvRepository $repository)
    {
        $this->repository = $repository;
    }

    public function saveUserInv(UserInv $model, UserInvForm $form): void
    {        
       $model->setUser_id($form->getUser_id());
       $model->setType($form->getType());
       $model->setActive($form->getActive());
       $model->setLanguage($form->getLanguage());
       $model->setAll_clients($form->getAll_clients());
       $model->setName($form->getName());
       $model->setCompany($form->getCompany());
       $model->setAddress_1($form->getAddress_1());
       $model->setAddress_2($form->getAddress_2());
       $model->setCity($form->getCity());
       $model->setState($form->getState());
       $model->setZip($form->getZip());
       $model->setCountry($form->getCountry());
       $model->setPhone($form->getPhone());
       $model->setFax($form->getFax());
       $model->setMobile($form->getMobile());
       $model->setEmail($form->getEmail());
       $model->setPassword($form->getPassword());
       $model->setWeb($form->getWeb());
       $model->setVat_id($form->getVat_id());
       $model->setTax_code($form->getTax_code());
       $model->setSalt($form->getSalt());
       $model->setPasswordreset_token($form->getPasswordreset_token());
       $model->setSubscribernumber($form->getSubscribernumber());
       $model->setIban($form->getIban());
       $model->setGln($form->getGln());
       $model->setRcc($form->getRcc());
       if ($model->isNewRecord()) {
            $model->setActive(false);
       }
       $this->repository->save($model);
    }
    
    public function deleteUserInv(UserInv $model): void
    {
        $this->repository->delete($model);
    }
}