<?php

declare(strict_types=1); 

namespace App\Invoice\UserCustom;

use App\Invoice\Entity\UserCustom;


final class UserCustomService
{

    private UserCustomRepository $repository;

    public function __construct(UserCustomRepository $repository)
    {
        $this->repository = $repository;
    }

    public function saveUserCustom(UserCustom $model, UserCustomForm $form): void
    {
        
       $model->setUser_id($form->getUser_id());
       $model->setFieldid($form->getFieldid());
       $model->setFieldvalue($form->getFieldvalue());
 
        $this->repository->save($model);
    }
    
    public function deleteUserCustom(UserCustom $model): void
    {
        $this->repository->delete($model);
    }
}