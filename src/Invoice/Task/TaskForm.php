<?php

declare(strict_types=1);

namespace App\Invoice\Task;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;
use \DateTimeImmutable;
use \DateTime;

final class TaskForm extends FormModel
{   
    private ?int $project_id=null;
    private ?string $task_name='';
    private string $task_description='';
    private ?float $task_price=null;
    private ?string $task_finish_date=null;
    private bool $task_status=false;
    private ?int $tax_rate_id=null;
    
    public function getProject_id() : int
    {
      return $this->project_id;
    }

    public function getTask_name() : string
    {
      return $this->task_name;
    }

    public function getTask_description() : string
    {
      return $this->task_description;
    }

    public function getTask_price() : float
    {
      return $this->task_price;
    }

    public function getTask_finish_date(): ?\DateTime
    {
        if (isset($this->task_finish_date) && !empty($this->task_finish_date)){
            return new DateTime($this->task_finish_date);            
        }        
        if (empty($this->task_finish_date)){
            return $this->task_finish_date = null;
        } 
    }
    
    public function getTask_status() : bool
    {
      return $this->task_status;
    }

    public function getTax_rate_id() : int
    {
      return $this->tax_rate_id;
    }

    public function getFormName(): string
    {
      return '';
    }

    public function getRules(): array    {
      return [
        'project_id' => [
            Required::rule(),
        ],
        'task_name' => [
            Required::rule(),
        ],
        'task_description' => [
            Required::rule(),
        ],
        'task_price' => [
            Required::rule(),
        ],
        'task_finish_date' => [
            Required::rule(),
        ],
        'task_status' => [
            Required::rule(),
        ],
        'tax_rate_id' => [
            Required::rule(),
        ],
    ];
}
}
