<?php

declare(strict_types=1); 

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use App\Invoice\Entity\Quote;
use App\Invoice\Entity\TaxRate;
  
 /**
 * @Entity(
 * repository="App\Invoice\QuoteTaxRate\QuoteTaxRateRepository",
 * )
 */
 
 class QuoteTaxRate
 {
       
   
    /**
     * @BelongsTo(target="Quote", nullable=false, fkAction="NO ACTION")
     *
     * @var \Cycle\ORM\Promise\Reference|Quote
     */
     private $quote = null;
    

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
     private ?int $quote_id =  null;
     
    /**
     * @Column(type="integer(11)", nullable=false)
     */
     private ?int $tax_rate_id =  null;
     
    /**
     * @Column(type="integer(1)", nullable=false,default=0)
     */
     private ?int $include_item_tax =  null;
     
    /**
     * @Column(type="decimal(20,2)", nullable=true)
     */
     private ?float $quote_tax_rate_amount =  null;
     
     public function __construct(
         int $id = null,
         int $quote_id = null,
         int $tax_rate_id = null,
         int $include_item_tax = null,
         float $quote_tax_rate_amount = null
     )
     {
         $this->id=$id;
         $this->quote_id=$quote_id;
         $this->tax_rate_id=$tax_rate_id;
         $this->include_item_tax=$include_item_tax;
         $this->quote_tax_rate_amount=$quote_tax_rate_amount;
     }
    
    public function getQuote() : ?Quote
 {
      return $this->quote;
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
    
    public function getQuote_id(): string
    {
     return (string)$this->quote_id;
    }
    
    public function setQuote_id(int $quote_id) : void
    {
      $this->quote_id =  $quote_id;
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
    
    public function getQuote_tax_rate_amount(): ?float
    {
       return $this->quote_tax_rate_amount;
    }
    
    public function setQuote_tax_rate_amount(float $quote_tax_rate_amount) : void
    {
      $this->quote_tax_rate_amount =  $quote_tax_rate_amount;
    }
}