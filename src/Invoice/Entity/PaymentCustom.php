<?php

declare(strict_types=1); 

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use App\Invoice\Entity\Payment;
use App\Invoice\Entity\CustomField;

#[Entity(repository: \App\Invoice\PaymentCustom\PaymentCustomRepository::class)]
class PaymentCustom
{
    #[BelongsTo(target:Payment::class, nullable: false)]
    private ?Payment $payment = null;
    
    #[BelongsTo(target:CustomField::class, nullable: false)]
    private ?CustomField $custom_field = null;
        
    #[Column(type: 'primary')]
    private ?int $id =  null;
     
    #[Column(type: 'integer(11)', nullable:false)]
    private ?int $payment_id =  null;
     
    #[Column(type: 'integer(11)', nullable:false)]
    private ?int $custom_field_id =  null;
     
    #[Column(type: 'text', nullable:true)]
    private string $value =  '';
     
    public function __construct(
        int $id = null,
        int $payment_id = null,
        int $custom_field_id = null,
        string $value = ''
    )
    {
        $this->id=$id;
        $this->payment_id=$payment_id;
        $this->custom_field_id=$custom_field_id;
        $this->value=$value;
    }
    
    public function getPayment() : ?Payment
    {
      return $this->payment;
    }
    
    public function getCustomField() : ?CustomField
    {
      return $this->custom_field;
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
    
    public function getCustom_field_id(): string
    {
      return (string)$this->custom_field_id;
    }
    
    public function setCustom_field_id(int $custom_field_id) : void
    {
      $this->custom_field_id =  $custom_field_id;
    }
    
    public function getValue(): string
    {
       return $this->value;
    }
    
    public function setValue(string $value) : void
    {
      $this->value =  $value;
    }
}