<?php

declare(strict_types=1); 

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use App\Invoice\Entity\Inv;
use App\Invoice\Entity\TaxRate;

#[Entity(repository: \App\Invoice\InvTaxRate\InvTaxRateRepository::class)]

class InvTaxRate
{   
    #[BelongsTo(target:Inv::class, nullable: false, fkAction: 'NO ACTION')]
    private ?Inv $inv = null;
    
    #[BelongsTo(target:TaxRate::class, nullable: false, fkAction: 'NO ACTION')]
    private ?TaxRate $tax_rate = null;
    
    #[Column(type: 'primary')]
    private ?int $id =  null;
    
    #[Column(type: 'integer(11)', nullable:false)]
    private ?int $inv_id =  null;
     
    #[Column(type: 'integer(11)', nullable:false)]
    private ?int $tax_rate_id =  null;
     
    #[Column(type: 'integer(1)', nullable:false, default:0)]
    private ?int $include_item_tax =  null;
     
    #[Column(type: 'decimal(20,2)', nullable: false, default: 0.00)]
    private ?float $inv_tax_rate_amount= 0.00;
     
    public function __construct(
         int $id = null,
         int $inv_id = null,
         int $tax_rate_id = null,
         int $include_item_tax = null,
         float $inv_tax_rate_amount = 0.00
    )
    {
         $this->id=$id;
         $this->inv_id=$inv_id;
         $this->tax_rate_id=$tax_rate_id;
         $this->include_item_tax=$include_item_tax;
         $this->inv_tax_rate_amount=$inv_tax_rate_amount;
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
    
    public function getInv_tax_rate_amount(): ?float
    {
       return $this->inv_tax_rate_amount;
    }
    
    public function setInv_tax_rate_amount(float $inv_tax_rate_amount) : void
    {
      $this->inv_tax_rate_amount =  $inv_tax_rate_amount;
    }
}