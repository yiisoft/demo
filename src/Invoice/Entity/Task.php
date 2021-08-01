<?php

declare(strict_types=1); 

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use DateTime;
use DateTimeImmutable;

/**
 * @Entity( 
 * repository="App\Invoice\Task\TaskRepository",
 * )
 */
 
 class Task
 { 
     /**
     * @Column(type="primary", nullable=false)
     */
     public ?int $id =  null;
     
    /**
     * @Column(type="integer", nullable=false)
     */
     private ?int $project_id =  null;
     
    /**
     * @Column(type="text", nullable=true)
     */
     public ?string $task_name =  '';
     
    /**
     * @Column(type="longText", nullable=false)
     */
     public string $task_description =  '';
     
    /**
     * @Column(type="decimal(20,2)", nullable=true)
     */
     private ?float $task_price =  null;
     
    /**
     * @Column(type="date", nullable=false)
     */
     private $task_finish_date =  '';
     
    /**
     * @Column(type="boolean", nullable=false)
     */
     private bool $task_status = false;
     
    /**
     * @Column(type="integer", nullable=false)
     */
     private ?int $tax_rate_id =  null;
     
     public function __construct(
         int $project_id = null,
         string $task_name = '',
         string $task_description = '',
         float $task_price = null,
         $task_finish_date = '',
         bool $task_status = false,
         int $tax_rate_id = null
     )
     {
         $this->project_id=$project_id;
         $this->task_name=$task_name;
         $this->task_description=$task_description;
         $this->task_price=$task_price;
         $this->task_finish_date=$task_finish_date;
         $this->task_status=$task_status;
         $this->tax_rate_id=$tax_rate_id;
     }
    
    public function getId(): string
    {
      return (string)$this->id;
    }
    
    public function getProject_id(): int
    {
      return $this->project_id;
    }
    
    public function setProject_id(int $project_id) : void
    {
      $this->project_id =  $project_id;
    }
    
    public function getTask_name(): string
    {
      return $this->task_name;
    }
    
    public function setTask_name(string $task_name) : void
    {
      $this->task_name =  $task_name;
    }
    
    public function getTask_description(): string
    {
      return $this->task_description;
    }
    
    public function setTask_description(string $task_description) : void
    {
      $this->task_description =  $task_description;
    }
    
    public function getTask_price(): float
    {
      return $this->task_price;
    }
    
    public function setTask_price(float $task_price) : void
    {
      $this->task_price =  $task_price;
    }
    
    public function getTask_finish_date(): ?DateTimeImmutable  
    {
       if (isset($this->task_finish_date) && !empty($this->task_finish_date)){
            return $this->task_finish_date;            
        }
        if (empty($this->task_finish_date)){
            return $this->task_finish_date = null;
        }
    }
    
    public function setTask_finish_date(?DateTime $task_finish_date): void
    {
      $this->task_finish_date =  $task_finish_date;
    }
    
    public function getTask_status(): bool
    {
      return $this->task_status;
    }
    
    public function setTask_status(bool $task_status) : void
    {
      $this->task_status =  $task_status;
    }
    
    public function getTax_rate_id(): int
    {
      return $this->tax_rate_id;
    }
    
    public function setTax_rate_id(int $tax_rate_id) : void
    {
      $this->tax_rate_id =  $tax_rate_id;
    }
}
                     
    
