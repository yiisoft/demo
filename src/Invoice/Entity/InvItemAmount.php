<?php

declare(strict_types=1); 

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use App\Invoice\Entity\InvItem;

#[Entity(repository: \App\Invoice\InvItemAmount\InvItemAmountRepository::class)]
class InvItemAmount
{
    #[BelongsTo(target:InvItem::class, nullable: false)]
    private ?InvItem $inv_item = null;
        
    #[Column(type: 'primary')]
    private ?int $id =  null;
     
    #[Column(type: 'integer(11)', nullable: false)]
    private ?int $inv_item_id =  null;
     
    #[Column(type: 'decimal(20,2)', nullable: false, default: 0.00)]
    private ?float $subtotal =  0.00;
     
    #[Column(type: 'decimal(20,2)', nullable: false, default: 0.00)]
    private ?float $tax_total =  0.00;
     
    #[Column(type: 'decimal(20,2)', nullable: false, default: 0.00)]
    private ?float $discount =  0.00;
     
    #[Column(type: 'decimal(20,2)', nullable: false, default: 0.00)]
    private ?float $total =  0.00;
     
    public function __construct(
        int   $id = null,
        int   $inv_item_id = null,
        float $subtotal = 0.00,
        float $tax_total = 0.00,
        float $discount = 0.00,
        float $total = 0.00
    )
    {
        $this->id=$id;
        $this->inv_item_id=$inv_item_id;
        $this->subtotal=$subtotal;
        $this->tax_total=$tax_total;
        $this->discount=$discount;
        $this->total=$total;
    }
    
    public function getInvItem() : ?InvItem
    {
      return $this->inv_item;
    }
    
    public function getId(): string
    {
     return (string)$this->id;
    }
    
    public function setId(int $id) : void
    {
      $this->id =  $id;
    }
    
    public function getInv_item_id(): string
    {
     return (string)$this->inv_item_id;
    }
    
    public function setInv_item_id(int $inv_item_id) : void
    {
      $this->inv_item_id =  $inv_item_id;
    }
    
    public function getSubtotal(): ?float
    {
       return $this->subtotal;
    }
    
    public function setSubtotal(float $subtotal) : void
    {
      $this->subtotal =  $subtotal;
    }
    
    public function getTax_total(): ?float
    {
       return $this->tax_total;
    }
    
    public function setTax_total(float $tax_total) : void
    {
      $this->tax_total =  $tax_total;
    }
    
    public function getDiscount(): ?float
    {
       return $this->discount;
    }
    
    public function setDiscount(float $discount) : void
    {
      $this->discount =  $discount;
    }
    
    public function getTotal(): ?float
    {
       return $this->total;
    }
    
    public function setTotal(float $total) : void
    {
      $this->total =  $total;
    }
}