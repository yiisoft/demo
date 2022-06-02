<?php

declare(strict_types=1); 

namespace App\Invoice\Task;

use App\Invoice\Entity\Task;


final class TaskService
{

    private TaskRepository $repository;

    public function __construct(TaskRepository $repository)
    {
        $this->repository = $repository;
    }

    public function saveTask(Task $model, TaskForm $form): void
    {
        
       $model->setProject_id($form->getProject_id());
       $model->setName($form->getName());
       $model->setDescription($form->getDescription());
       $model->setPrice($form->getPrice());
       $model->setFinish_date($form->getFinish_date());
       $model->setStatus($form->getStatus());
       $model->setTax_rate_id($form->getTax_rate_id());
 
        $this->repository->save($model);
    }
    
    public function deleteTask(Task $model): void
    {
        $this->repository->delete($model);
    }
}