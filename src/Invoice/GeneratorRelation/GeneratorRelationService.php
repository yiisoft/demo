<?php

declare(strict_types=1);

namespace App\Invoice\GeneratorRelation;

use App\Invoice\Entity\GentorRelation;

final class GeneratorRelationService
{
    private GeneratorRelationRepository $repository;

    public function __construct(GeneratorRelationRepository $repository)
    {
        $this->repository = $repository;;
    }

    public function saveGeneratorRelation(GentorRelation $model, GeneratorRelationForm $form): void
    {
        $model->setLowercase_name($form->getLowercase_name());
        $model->setCamelcase_name($form->getCamelcase_name());
        $model->setView_field_name($form->getView_field_name());
        $model->setGentor_id($form->getGentor_id());
        $this->repository->save($model);
    }
    
    public function deleteGeneratorRelation(GentorRelation $model): void
    {
        $this->repository->delete($model);
    }
}
