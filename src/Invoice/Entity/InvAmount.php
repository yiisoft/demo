<?php

declare(strict_types=1); 

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use App\Invoice\Entity\Inv;

#[Entity(repository: \App\Invoice\InvAmount\InvAmountRepository::class)] 
class InvAmount
{
    
    #[BelongsTo(target:Inv::class, nullable: false, fkAction: 'NO ACTION')]
    private ?Inv $inv = null;
    
    #[Column(type: 'primary')]
    private ?int $id =  null;
     
    #[Column(type: 'integer(11)', nullable: false)]
    private ?int $inv_id =  null;
    
    #[Column(type: 'enum(1,-1)', nullable: false, default: '1')]
    private string $sign =  '1';
    
    #[Column(type: 'decimal(20,2)', nullable: false, default: 0.00)]
    private ?float $item_subtotal =  0.00;
     
    #[Column(type: 'decimal(20,2)', nullable: false, default: 0.00)]
    private ?float $item_tax_total =  0.00;
     
    #[Column(type: 'decimal(20,2)', nullable: false, default: 0.00)]
    private ?float $tax_total =  0.00;
     
    #[Column(type: 'decimal(20,2)', nullable: false, default: 0.00)]
    private ?float $total =  0.00;
     
    #[Column(type: 'decimal(20,2)', nullable: false, default: 0.00)]
    private ?float $paid =  0.00;
     
    #[Column(type: 'decimal(20,2)', nullable: false, default: 0.00)]
    private ?float $balance =  0.00;
    
    public function __construct(
         int $inv_id = null,
         string $sign = '',
         float $item_subtotal = 0.00,
         float $item_tax_total = 0.00,
         float $tax_total = 0.00,
         float $total = 0.00,
         float $paid = 0.00,
         float $balance = 0.00
    )
    {
         $this->inv_id=$inv_id;
         $this->sign=$sign;
         $this->item_subtotal=$item_subtotal;
         $this->item_tax_total=$item_tax_total;
         $this->tax_total=$tax_total;
         $this->total=$total;
         $this->paid=$paid;
         $this->balance=$balance;
    }
    
    public function getInv() : ?Inv
    {
      return $this->inv;
    }
    
    public function getId(): ?string
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
    
    public function getSign(): string
    {
     return $this->sign;
    }
    
    public function setSign(string $sign) : void
    {
      $this->sign =  $sign;
    }
    
    public function getItem_subtotal() : float
    {
     return $this->item_subtotal;
    }
    
    public function setItem_subtotal(float $item_subtotal) : void
    {
      $this->item_subtotal =  $item_subtotal;
    }
    
    public function getItem_tax_total() : float
    {
     return $this->item_tax_total;
    }
    
    public function setItem_tax_total(float $item_tax_total) : void
    {
      $this->item_tax_total =  $item_tax_total;
    }
    
    public function getTax_total(): float
    {
     return $this->tax_total;
    }
    
    public function setTax_total(float $tax_total) : void
    {
      $this->tax_total =  $tax_total;
    }
    
    public function getTotal(): float
    {
     return $this->total;
    }
    
    public function setTotal(float $total) : void
    {
      $this->total =  $total;
    }
    
    public function getPaid(): float
    {
     return $this->paid;
    }
    
    public function setPaid(float $paid) : void
    {
      $this->paid =  $paid;
    }
    
    public function getBalance(): float
    {
     return $this->balance;
    }
    
    public function setBalance(float $balance) : void
    {
      $this->balance =  $balance;
    }
}