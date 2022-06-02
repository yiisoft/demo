<?php
declare(strict_types=1); 

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use DateTime;
use DateTimeImmutable;
use App\Invoice\Entity\Inv;
use App\Invoice\Entity\PaymentMethod;

#[Entity(repository: \App\Invoice\Payment\PaymentRepository::class)] 
class Payment
{  
    #[BelongsTo(target:Inv::class, nullable: false, fkAction: 'NO ACTION')]
    private ?Inv $inv = null;    
    
    #[BelongsTo(target:PaymentMethod::class, nullable: false, fkAction: 'NO ACTION')]
    private ?PaymentMethod $payment_method = null;    
    
    #[Column(type: 'primary')]
    private ?int $id =  null;
    
    #[Column(type: 'integer(11)', nullable:false, default:0)]
    private ?int $payment_method_id =  null;
    
    #[Column(type: 'date', nullable: true)]
    private $payment_date;
    
    #[Column(type: 'decimal(20,2)', nullable:true, default: 0.00)]
    private ?float $amount =  0.00;
    
    #[Column(type: 'longText', nullable:false)]
    private string $note =  '';
    
    #[Column(type: 'integer(11)', nullable:false)]
    private ?int $inv_id =  null;
     
    public function __construct(
        int $id = null,
        int $inv_id = null,
        int $payment_method_id = null,
        $payment_date = '',
        float $amount = 0.00,
        string $note = ''
    )
    {
        $this->id=$id;
        $this->inv_id=$inv_id;
        $this->payment_method_id=$payment_method_id;
        $this->payment_date=$payment_date;
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
    
    public function getPayment_date(): ?DateTimeImmutable
    {
      if (isset($this->payment_date) && !empty($this->payment_date)){
       return $this->payment_date;
      }
      if (empty($this->payment_date)){
       return $this->payment_date = null;
      }
    }
    
    public function setPayment_date(?DateTime $payment_date) : void
    {
      $this->payment_date =  $payment_date;
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