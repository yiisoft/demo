<?php

declare(strict_types=1); 

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use App\Invoice\Entity\QuoteItem;

#[Entity(repository: \App\Invoice\QuoteItemAmount\QuoteItemAmountRepository::class)]
class QuoteItemAmount
{
    #[BelongsTo(target:QuoteItem::class, nullable: false)]
    private ?QuoteItem $quote_item = null;
        
    #[Column(type: 'primary')]
    private ?int $id =  null;
     
    #[Column(type: 'integer(11)', nullable: false)]
    private ?int $quote_item_id =  null;
     
    #[Column(type: 'decimal(20,2)', nullable: false, default: 0.00)]
    private ?float $subtotal =  0.00;
     
    #[Column(type: 'decimal(20,2)', nullable: false, default: 0.00)]
    private ?float $tax_total = 0.00 ;
     
    #[Column(type: 'decimal(20,2)', nullable: false, default: 0.00)]
    private ?float $discount =  0.00;
     
    #[Column(type: 'decimal(20,2)', nullable: false, default: 0.00)]
    private ?float $total =  0.00;
     
    public function __construct(
        int   $id = null,
        int   $quote_item_id = null,
        float $subtotal = 0.00,
        float $tax_total = 0.00,
        float $discount = 0.00,
        float $total = 0.00
    )
    {
        $this->id=$id;
        $this->quote_item_id=$quote_item_id;
        $this->subtotal=$subtotal;
        $this->tax_total=$tax_total;
        $this->discount=$discount;
        $this->total=$total;
    }
    
    public function getQuoteItem() : ?QuoteItem
    {
      return $this->quote_item;
    }
    
    public function getId(): string
    {
     return (string)$this->id;
    }
    
    public function setId(int $id) : void
    {
      $this->id =  $id;
    }
    
    public function getQuote_item_id(): string
    {
     return (string)$this->quote_item_id;
    }
    
    public function setQuote_item_id(int $quote_item_id) : void
    {
      $this->quote_item_id =  $quote_item_id;
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