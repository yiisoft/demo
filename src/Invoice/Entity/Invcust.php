<?php

declare(strict_types=1); 

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use App\Invoice\Entity\Inv;
  
 /**
* @Entity(
 * repository="App\Invoice\Invcust\InvcustRepository",
 * )
 */
 
 class Invcust
 {
       
   
    /**
     * @BelongsTo(target="Inv", nullable=false, fkAction="NO ACTION")
     *
     * @var \Cycle\ORM\Promise\Reference|Inv
     */
     private $inv = null;
    
    
        /**
     * @Column(type="primary")
     */
     public ?int $id =  null;
     
    /**
     * @Column(type="integer(11)", nullable=false)
     */
     private ?int $inv_id =  null;
     
    /**
     * @Column(type="integer(11)", nullable=false)
     */
     private ?int $fieldid =  null;
     
    /**
     * @Column(type="text", nullable=true)
     */
     private ?string $fieldvalue =  '';
     
     public function __construct(
          int $id = null,
         int $inv_id = null,
         int $fieldid = null,
         string $fieldvalue = ''
     )
     {
         $this->id=$id;
         $this->inv_id=$inv_id;
         $this->fieldid=$fieldid;
         $this->fieldvalue=$fieldvalue;
     }
    
    public function getInv() : ?Inv
 {
      return $this->inv;
    }
    
    public function getId(): string
    {
     return (string)$this->id;
    }
    
    public function setId(int $id) : void
    {
      $this->id =  $id;
    }
    
    public function getInv_id(): string
    {
     return (string)$this->inv_id;
    }
    
    public function setInv_id(int $inv_id) : void
    {
      $this->inv_id =  $inv_id;
    }
    
    public function getFieldid(): string
    {
     return (string)$this->fieldid;
    }
    
    public function setFieldid(int $fieldid) : void
    {
      $this->fieldid =  $fieldid;
    }
    
    public function getFieldvalue(): ?string
    {
       return $this->fieldvalue;
    }
    
    public function setFieldvalue(string $fieldvalue) : void
    {
      $this->fieldvalue =  $fieldvalue;
    }
}