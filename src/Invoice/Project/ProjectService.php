<?php

declare(strict_types=1); 

namespace App\Invoice\Project;

use App\Invoice\Entity\Project;


final class ProjectService
{

    private ProjectRepository $repository;

    public function __construct(ProjectRepository $repository)
    {
        $this->repository = $repository;
    }

    public function saveProject(Project $model, ProjectForm $form): void
    {
        
       $model->setClient_id($form->getClient_id());
       $model->setName($form->getName());
 
        $this->repository->save($model);
    }
    
    public function deleteProject(Project $model): void
    {
        $this->repository->delete($model);
    }
}