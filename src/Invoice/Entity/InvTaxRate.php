<?php

declare(strict_types=1); 

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use App\Invoice\Entity\Inv;
use App\Invoice\Entity\TaxRate;
  
 /**
 * @Entity(
 * repository="App\Invoice\InvTaxRate\InvTaxRateRepository",
 * )
 */
 
 class InvTaxRate
 {
       
   
    /**
     * @BelongsTo(target="Inv", nullable=false, fkAction="NO ACTION")
     *
     * @var \Cycle\ORM\Promise\Reference|Inv
     */
     private $inv = null;
    

    /**
     * @BelongsTo(target="TaxRate", nullable=false, fkAction="NO ACTION")
     *
     * @var \Cycle\ORM\Promise\Reference|TaxRate
     */
     private $tax_rate = null;
    
    
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
     private ?int $tax_rate_id =  null;
     
    /**
     * @Column(type="integer(1)", nullable=false,default=0)
     */
     private ?int $include_item_tax =  null;
     
    /**
     * @Column(type="decimal(10,2)", nullable=false,default=0)
     */
     private ?float $amount =  null;
     
     public function __construct(
         int $id = null,
         int $inv_id = null,
         int $tax_rate_id = null,
         int $include_item_tax = null,
         float $amount = null
     )
     {
         $this->id=$id;
         $this->inv_id=$inv_id;
         $this->tax_rate_id=$tax_rate_id;
         $this->include_item_tax=$include_item_tax;
         $this->amount=$amount;
     }
    
    public function getInv() : ?Inv
 {
      return $this->inv;
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
    
    public function getInv_id(): string
    {
     return (string)$this->inv_id;
    }
    
    public function setInv_id(int $inv_id) : void
    {
      $this->inv_id =  $inv_id;
    }
    
    public function getTax_rate_id(): string
    {
     return (string)$this->tax_rate_id;
    }
    
    public function setTax_rate_id(int $tax_rate_id) : void
    {
      $this->tax_rate_id =  $tax_rate_id;
    }
    
    public function getInclude_item_tax(): int
    {
       return $this->include_item_tax;
    }
    
    public function setInclude_item_tax(int $include_item_tax) : void
    {
      $this->include_item_tax =  $include_item_tax;
    }
    
    public function getAmount(): float
    {
       return $this->amount;
    }
    
    public function setAmount(float $amount) : void
    {
      $this->amount =  $amount;
    }
}