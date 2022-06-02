<?php

declare(strict_types=1); 

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
  
#[Entity(repository: \App\Invoice\ItemLookup\ItemLookupRepository::class)]
class ItemLookup
{
    #[Column(type: 'primary')]
    private ?int $id =  null;     
    
    #[Column(type: 'string(100)', nullable:false)]
    private string $name =  '';     
    
    #[Column(type: 'longText', nullable:false)]
    private string $description =  '';
    
    #[Column(type: 'decimal(10,2)', nullable:false)]
    private ?float $price =  null;
     
    public function __construct(
        int $id = null,
        string $name = '',
        string $description = '',
        float $price = null
    )
    {
         $this->id=$id;
         $this->name=$name;
         $this->description=$description;
         $this->price=$price;
    }
    
    public function getId(): string
    {
     return (string)$this->id;
    }
    
    public function setId(int $id) : void
    {
      $this->id =  $id;
    }
    
    public function getName(): string
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
    
    public function getPrice(): float
    {
       return $this->price;
    }
    
    public function setPrice(float $price) : void
    {
      $this->price =  $price;
    }
}