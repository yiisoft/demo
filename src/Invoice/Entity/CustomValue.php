<?php

declare(strict_types=1); 

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
  
 /**
* @Entity(
 * repository="App\Invoice\CustomValue\CustomValueRepository",
 * )
 */
 
 class CustomValue
 {
       
       
        /**
     * @Column(type="primary")
     */
     public ?int $id =  null;
     
    /**
     * @Column(type="integer(11)", nullable=false)
     */
     private ?int $field =  null;
     
    /**
     * @Column(type="text", nullable=false)
     */
     private string $value =  '';
     
     public function __construct(
          int $id = null,
         int $field = null,
         string $value = ''
     )
     {
         $this->id=$id;
         $this->field=$field;
         $this->value=$value;
     }
    
    public function getId(): string
    {
     return (string)$this->id;
    }
    
    public function setId(int $id) : void
    {
      $this->id =  $id;
    }
    
    public function getField(): int
    {
       return $this->field;
    }
    
    public function setField(int $field) : void
    {
      $this->field =  $field;
    }
    
    public function getValue(): string
    {
       return $this->value;
    }
    
    public function setValue(string $value) : void
    {
      $this->value =  $value;
    }
}