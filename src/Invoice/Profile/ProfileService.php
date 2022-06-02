<?php

declare(strict_types=1); 

namespace App\Invoice\Profile;

use App\Invoice\Entity\Profile;
use App\Invoice\Profile\ProfileForm;
use App\Invoice\Profile\ProfileRepository;


final class ProfileService
{

    private ProfileRepository $repository;

    public function __construct(ProfileRepository $repository)
    {
        $this->repository = $repository;
    }

    public function saveProfile(Profile $model, ProfileForm $form): void
    {
        
       $model->setCompany_id($form->getCompany_id());
       $model->setCurrent($form->getCurrent());
       $model->setMobile($form->getMobile());
       $model->setEmail($form->getEmail());
 
        $this->repository->save($model);
    }
    
    public function deleteProfile(Profile $model): void
    {
        $this->repository->delete($model);
    }
}