<?php

declare(strict_types=1);

namespace App\Invoice\Task;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;
use \DateTime;
use \DateTimeImmutable;

final class TaskForm extends FormModel
{    
    
    private ?int $project_id=null;
    private ?string $name='';
    private ?string $description='';
    private ?float $price=null;
    private ?string $finish_date='';
    private ?bool $status=false;
    private ?int $tax_rate_id=null;

    public function getProject_id() : int
    {
      return $this->project_id;
    }

    public function getName() : string
    {
      return $this->name;
    }

    public function getDescription() : string
    {
      return $this->description;
    }

    public function getPrice() : float
    {
      return $this->price;
    }

    public function getFinish_date() : ?\DateTime
    {
        return new DateTime($this->finish_date);
       
    }

    public function getStatus() : bool
    {
      return $this->status;
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
        'name' => [new Required()],
        'description' => [new Required()],
        'price' => [new Required()],
        'finish_date' => [new Required()],
    ];
}
}
