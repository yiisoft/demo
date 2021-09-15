<?php

declare(strict_types=1);

namespace App\Invoice\Payment;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;
use \DateTime;
use \DateTimeImmutable;

final class PaymentForm extends FormModel
{    
    
    private ?int $invoice_id=null;
    private ?int $payment_method_id=null;
    private ?string $date='';
    private ?float $amount=null;
    private ?string $note='';
    private ?int $inv_id=null;

    public function getInvoice_id() : int
    {
      return $this->invoice_id;
    }

    public function getPayment_method_id() : int
    {
      return $this->payment_method_id;
    }

    public function getDate() : ?\DateTime
    {
       if (isset($this->date) && !empty($this->date)) {
          return new DateTime($this->date);
       }
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
        'date' => [
            Required::rule(),
        ],
        'amount' => [
            Required::rule(),
        ],
        'note' => [
            Required::rule(),
        ],
    ];
}
}
