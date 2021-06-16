<?php

declare(strict_types=1);

namespace App\Invoice\Family;

use App\Invoice\Entity\Family;

final class FamilyService
{
    private FamilyRepository $repository;

    public function __construct(FamilyRepository $repository)
    {
        $this->repository = $repository;;
    }

    public function saveFamily(Family $model, FamilyForm $form): void
    {
        $model->setFamily_name($form->getFamily_name());
        $this->repository->save($model);
    }
    
    public function deleteFamily(Family $model): void
    {
        $this->repository->delete($model);
    }
}
