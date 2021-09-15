<?php

declare(strict_types=1); 

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use App\Invoice\Entity\Inv;
  
/**
 * @Entity(
 * repository="App\Invoice\InvAmount\InvAmountRepository",
 * )
 */
 
 class InvAmount
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
     * @Column(type="enum(1,-1)", nullable=false)
     */
     private string $sign =  '';
     
    /**
     * @Column(type="decimal(20,2)", nullable=true)
     */
     private ?float $item_sub_total =  null;
     
    /**
     * @Column(type="decimal(20,2)", nullable=true)
     */
     private ?float $item_tax_total =  null;
     
    /**
     * @Column(type="decimal(20,2)", nullable=true)
     */
     private ?float $tax_total =  null;
     
    /**
     * @Column(type="decimal(20,2)", nullable=true)
     */
     private ?float $invoice_total =  null;
     
    /**
     * @Column(type="decimal(20,2)", nullable=true)
     */
     private ?float $invoice_paid =  null;
     
    /**
     * @Column(type="decimal(20,2)", nullable=true)
     */
     private ?float $invoice_balance =  null;
     
     /**
     * @param int $inv_id
     * @param string $sign
     * @param float $item_sub_total
     * @param float $item_tax_total
     * @param float $tax_total
     * @param float $invoice_total
     * @param float $invoice_paid
     * @param float $invoice_balance
     */
     
     public function __construct(
         int $inv_id = null,
         string $sign = '',
         float $item_sub_total = null,
         float $item_tax_total = null,
         float $tax_total = null,
         float $invoice_total = null,
         float $invoice_paid = null,
         float $invoice_balance = null
     )
     {
         $this->inv_id=$inv_id;
         $this->sign=$sign;
         $this->item_sub_total=$item_sub_total;
         $this->item_tax_total=$item_tax_total;
         $this->tax_total=$tax_total;
         $this->invoice_total=$invoice_total;
         $this->invoice_paid=$invoice_paid;
         $this->invoice_balance=$invoice_balance;
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
    
    public function getInv_id(): int
    {
     return $this->inv_id;
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
    
    public function getItem_sub_total() : float
    {
     return $this->item_sub_total;
    }
    
    public function setItem_sub_total(float $item_sub_total) : void
    {
      $this->item_sub_total =  $item_sub_total;
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
    
    public function getInvoice_total(): float
    {
     return $this->invoice_total;
    }
    
    public function setInvoice_total(float $invoice_total) : void
    {
      $this->invoice_total =  $invoice_total;
    }
    
    public function getInvoice_paid(): float
    {
     return $this->invoice_paid;
    }
    
    public function setInvoice_paid(float $invoice_paid) : void
    {
      $this->invoice_paid =  $invoice_paid;
    }
    
    public function getInvoice_balance(): float
    {
     return $this->invoice_balance;
    }
    
    public function setInvoice_balance(float $invoice_balance) : void
    {
      $this->invoice_balance =  $invoice_balance;
    }
}