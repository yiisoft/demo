<?php

declare(strict_types=1);

namespace App\Invoice\Payment;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;
use \DateTime;

final class PaymentForm extends FormModel
{    
    private ?int $payment_method_id=null;
    private ?string $payment_date='';
    private ?float $amount=null;
    private ?string $note='';
    private ?int $inv_id=null;

    public function getPayment_method_id() : int
    {
      return $this->payment_method_id;
    }

    public function getPayment_date() : ?\DateTime
    {
       return new DateTime($this->payment_date);      
    }

    public function getAmount() : float
    {
      return $this->amount;
    }

    public function getNote() : string
    {
      return $this->note;
    }

    public function getInv_id() : int
    {
      return $this->inv_id;
    }

    public function getFormName(): string
    {
      return '';
    }

    public function getRules(): array    {
      return [
        'payment_method_id' => [new Required()],
        'payment_date' => [new Required()],
        'amount' => [new Required()],
        'note' => [new Required()],
    ];
}
}
