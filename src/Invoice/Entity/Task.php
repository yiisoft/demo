<?php

declare(strict_types=1); 

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use DateTime;
use DateTimeImmutable;
use App\Invoice\Entity\Project;
use App\Invoice\Entity\TaxRate;
 
#[Entity(repository: \App\Invoice\Task\TaskRepository::class)]
class Task
{
    #[BelongsTo(target:Project::class, nullable: false, fkAction:'NO ACTION')]
    private ?Project $project = null;    
    
    #[BelongsTo(target:TaxRate::class, nullable: false, fkAction: 'NO ACTION')]
    private ?TaxRate $tax_rate = null;
    
    #[Column(type: 'primary')]
    private ?int $id =  null;
     
    #[Column(type:'integer(11)', nullable: false)] 
    private ?int $project_id =  null;
     
    #[Column(type:'text', nullable: true)] 
    private ?string $name =  '';
     
    #[Column(type:'longText', nullable: false)] 
    private string $description =  '';
     
    #[Column(type:'decimal(20,2)', nullable: true)] 
    private ?float $price =  null;
    
    #[Column(type:'date', nullable: false)] 
    private  $finish_date;
    
    #[Column(type:'boolean', nullable: false)] 
    private bool $status = false;
     
    #[Column(type:'integer(11)', nullable: false)]
    private ?int $tax_rate_id =  null;
     
    public function __construct(
        int $id = null,
        int $project_id = null,
        string $name = '',
        string $description = '',
        float $price = null,
        $finish_date = '',
        bool $status = false,
        int $tax_rate_id = null
    )
    {
        $this->id=$id;
        $this->project_id=$project_id;
        $this->name=$name;
        $this->description=$description;
        $this->price=$price;
        $this->finish_date=$finish_date;
        $this->status=$status;
        $this->tax_rate_id=$tax_rate_id;
    }
    
    public function getProject() : ?Project
    {
      return $this->project;
    }
    
    public function getTaxRate() : ?TaxRate
    {
      return $this->tax_rate;
    }
    
    public function getId(): string
    {
     return (string)$this->id;
    }
    
    public function setId(int $id) : void
    {
      $this->id =  $id;
    }
    
    public function getProject_id(): string
    {
     return (string)$this->project_id;
    }
    
    public function setProject_id(int $project_id) : void
    {
      $this->project_id =  $project_id;
    }
    
    public function getName(): ?string
    {
       return $this->name;
    }
    
    public function setName(string $name) : void
    {
      $this->name =  $name;
    }
    
    public function getDescription(): string
    {
       return $this->description;
    }
    
    public function setDescription(string $description) : void
    {
      $this->description =  $description;
    }
    
    public function getPrice(): ?float
    {
       return $this->price;
    }
    
    public function setPrice(float $price) : void
    {
      $this->price =  $price;
    }
    
    public function getFinish_date(): DateTimeImmutable
    {
      return $this->finish_date;
    }
    
    public function setFinish_date(DateTime $finish_date) : void
    {
      $this->finish_date =  $finish_date;
    }
    
    public function getStatus(): bool
    {
      return $this->status;
    }
    
    public function setStatus(bool $status) : void
    {
      $this->status =  $status;
    }
    
    public function getTax_rate_id(): string
    {
     return (string)$this->tax_rate_id;
    }
    
    public function setTax_rate_id(int $tax_rate_id) : void
    {
      $this->tax_rate_id =  $tax_rate_id;
    }
}