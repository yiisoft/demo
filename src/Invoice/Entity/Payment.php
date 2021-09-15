<?php

declare(strict_types=1); 

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use DateTime;
use DateTimeImmutable;use App\Invoice\Entity\Inv;
use App\Invoice\Entity\PaymentMethod;
  
 /**
 * @Entity(
 * repository="App\Invoice\Payment\PaymentRepository",
 * )
 */
 
 class Payment
 {
       
   
    /**
     * @BelongsTo(target="Inv", nullable=false, fkAction="NO ACTION")
     *
     * @var \Cycle\ORM\Promise\Reference|Inv
     */
     private $inv = null;
    

    /**
     * @BelongsTo(target="PaymentMethod", nullable=false, fkAction="NO ACTION")
     *
     * @var \Cycle\ORM\Promise\Reference|PaymentMethod
     */
     private $payment_method = null;
    
    
        /**
     * @Column(type="primary")
     */
     public ?int $id =  null;
     
    /**
     * @Column(type="integer(11)", nullable=false,default=0)
     */
     private ?int $payment_method_id =  null;
     
    /**
     * @Column(type="date", nullable=false)
     */
     private  $date;
     
    /**
     * @Column(type="decimal(20,2)", nullable=true)
     */
     private ?float $amount =  null;
     
    /**
     * @Column(type="longText", nullable=false)
     */
     private string $note =  '';
     
    /**
     * @Column(type="integer(11)", nullable=false)
     */
     private ?int $inv_id =  null;
     
     public function __construct(
         int $id = null,
         int $inv_id = null,
         int $payment_method_id = null,
          $date = '',
         float $amount = null,
         string $note = ''
     )
     {
         $this->id=$id;
         $this->inv_id=$inv_id;
         $this->payment_method_id=$payment_method_id;
         $this->date=$date;
         $this->amount=$amount;
         $this->note=$note;
     }
    
    public function getInv() : ?Inv
 {
      return $this->inv;
    }
    
    public function getPaymentMethod() : ?PaymentMethod
 {
      return $this->payment_method;
    }
    
    public function getId(): string
    {
     return (string)$this->id;
    }
    
    public function setId(int $id) : void
    {
      $this->id =  $id;
    }
    
    public function getPayment_method_id(): string
    {
     return (string)$this->payment_method_id;
    }
    
    public function setPayment_method_id(int $payment_method_id) : void
    {
      $this->payment_method_id =  $payment_method_id;
    }
    
    public function getDate(): DateTimeImmutable
    {
      if (isset($this->date) && !empty($this->date)){
       return $this->date;
     };
    }
    
    public function setDate(DateTime $date) : void
    {
      $this->date =  $date;
    }
    
    public function getAmount(): ?float
    {
       return $this->amount;
    }
    
    public function setAmount(float $amount) : void
    {
      $this->amount =  $amount;
    }
    
    public function getNote(): string
    {
       return $this->note;
    }
    
    public function setNote(string $note) : void
    {
      $this->note =  $note;
    }
    
    public function getInv_id(): string
    {
     return (string)$this->inv_id;
    }
    
    public function setInv_id(int $inv_id) : void
    {
      $this->inv_id =  $inv_id;
    }
}