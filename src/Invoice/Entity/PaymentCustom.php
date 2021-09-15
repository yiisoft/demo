<?php

declare(strict_types=1); 

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use App\Invoice\Entity\Payment;
  
 /**
 * @Entity(
 * repository="App\Invoice\PaymentCustom\PaymentCustomRepository",
 * )
 */
 
 class PaymentCustom
 {
       
   
    /**
     * @BelongsTo(target="Payment", nullable=false, fkAction="NO ACTION")
     *
     * @var \Cycle\ORM\Promise\Reference|Payment
     */
     private $payment = null;
    
    
        /**
     * @Column(type="primary")
     */
     public ?int $id =  null;
     
    /**
     * @Column(type="integer(11)", nullable=false)
     */
     private ?int $payment_id =  null;
     
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
         int $payment_id = null,
         int $fieldid = null,
         string $fieldvalue = ''
     )
     {
         $this->id=$id;
         $this->payment_id=$payment_id;
         $this->fieldid=$fieldid;
         $this->fieldvalue=$fieldvalue;
     }
    
    public function getPayment() : ?Payment
 {
      return $this->payment;
    }
    
    public function getId(): string
    {
     return (string)$this->id;
    }
    
    public function setId(int $id) : void
    {
      $this->id =  $id;
    }
    
    public function getPayment_id(): string
    {
     return (string)$this->payment_id;
    }
    
    public function setPayment_id(int $payment_id) : void
    {
      $this->payment_id =  $payment_id;
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