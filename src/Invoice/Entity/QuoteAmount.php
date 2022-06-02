<?php

declare(strict_types=1); 

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use App\Invoice\Entity\Quote;

#[Entity(repository: \App\Invoice\QuoteAmount\QuoteAmountRepository::class)]
class QuoteAmount
{
    #[BelongsTo(target:Quote::class, nullable: false, fkAction:'NO ACTION')]
    private ?Quote $quote = null;    
    
    #[Column(type: 'primary')]
    private ?int $id =  null;
     
    #[Column(type: 'integer(11)', nullable:false)]
    private ?int $quote_id =  null;
    
    #[Column(type: 'decimal(20,2)', nullable:false, default: 0.00)]
    private ?float $item_subtotal =  0.00;
     
    #[Column(type: 'decimal(20,2)', nullable:false, default: 0.00)]
    private ?float $item_tax_total =  0.00;
     
    #[Column(type: 'decimal(20,2)', nullable:false, default: 0.00)]
    private ?float $tax_total =  0.00;
     
    #[Column(type: 'decimal(20,2)', nullable:false, default: 0.00)]
    private ?float $total =  0.00;
     
    public function __construct(
        int $id = null,
        int $quote_id = null,
        float $item_subtotal = 0.00,
        float $item_tax_total = 0.00,
        float $tax_total = 0.00,
        float $total = 0.00
    )
    {
        $this->id=$id;
        $this->quote_id=$quote_id;
        $this->item_subtotal=$item_subtotal;
        $this->item_tax_total=$item_tax_total;
        $this->tax_total=$tax_total;
        $this->total=$total;
    }
    
    public function getQuote() : ?Quote
    {
      return $this->quote;
    }
    
    public function getId(): string
    {
     return (string)$this->id;
    }
    
    public function setId(int $id) : void
    {
      $this->id =  $id;
    }
    
    public function getQuote_id(): string
    {
     return (string)$this->quote_id;
    }
    
    public function setQuote_id(int $quote_id) : void
    {
      $this->quote_id =  $quote_id;
    }
    
    public function getItem_subtotal(): ?float
    {
       return $this->item_subtotal;
    }
    
    public function setItem_subtotal(float $item_subtotal) : void
    {
      $this->item_subtotal =  $item_subtotal;
    }
    
    public function getItem_tax_total(): ?float
    {
       return $this->item_tax_total;
    }
    
    public function setItem_tax_total(float $item_tax_total) : void
    {
      $this->item_tax_total =  $item_tax_total;
    }
    
    public function getTax_total(): ?float
    {
       return $this->tax_total;
    }
    
    public function setTax_total(float $tax_total) : void
    {
      $this->tax_total =  $tax_total;
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