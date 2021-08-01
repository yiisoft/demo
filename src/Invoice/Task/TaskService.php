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
       $model->setTask_name($form->getTask_name());
       $model->setTask_description($form->getTask_description());
       $model->setTask_price($form->getTask_price());
       $model->setTask_finish_date($form->getTask_finish_date());
       $model->setTask_status($form->getTask_status());
       $model->setTax_rate_id($form->getTax_rate_id());
       $this->repository->save($model);
    }
    
    public function deleteTask(Task $model): void
    {
        $this->repository->delete($model);
    }
}