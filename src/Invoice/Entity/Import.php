<?php

declare(strict_types=1); 

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use DateTimeImmutable;  

 #[Entity(repository: \App\Invoice\Import\ImportRepository::class)] 
 class Import
 {   
    #[Column(type: 'primary')]
    private ?int $id =  null;
     
    #[Column(type: 'datetime', nullable: false)]
    private DateIimeImmutable $date;
     
    public function __construct(
          int $id = null
    )
    {
         $this->id=$id;
         $this->date=new DateTimeImmutable();
    }
    
    public function getId(): string
    {
     return (string)$this->id;
    }
    
    public function setId(int $id) : void
    {
      $this->id =  $id;
    }
    
    public function getDate(): DateTimeImmutable
    {
       return $this->date;
    }
    
}