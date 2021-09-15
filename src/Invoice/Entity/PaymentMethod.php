<?php

declare(strict_types=1); 

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
  
 /**
 * @Entity(
 * repository="App\Invoice\PaymentMethod\PaymentMethodRepository",
 * )
 */
 
 class PaymentMethod
 {
       
       
        /**
     * @Column(type="primary")
     */
     public ?int $id =  null;
     
    /**
     * @Column(type="text", nullable=true)
     */
     private ?string $name =  '';
     
     public function __construct(
          int $id = null,
         string $name = ''
     )
     {
         $this->id=$id;
         $this->name=$name;
     }
    
    public function getId(): string
    {
     return (string)$this->id;
    }
    
    public function setId(int $id) : void
    {
      $this->id =  $id;
    }
    
    public function getName(): ?string
    {
       return $this->name;
    }
    
    public function setName(string $name) : void
    {
      $this->name =  $name;
    }
}