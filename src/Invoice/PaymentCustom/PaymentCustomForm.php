<?php

declare(strict_types=1);

namespace App\Invoice\PaymentCustom;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;

final class PaymentCustomForm extends FormModel
{    
    
    private ?int $payment_id=null;
    private ?int $fieldid=null;
    private ?string $fieldvalue='';

    public function getPayment_id() : int
    {
      return $this->payment_id;
    }

    public function getFieldid() : int
    {
      return $this->fieldid;
    }

    public function getFieldvalue() : string
    {
      return $this->fieldvalue;
    }

    public function getFormName(): string
    {
      return '';
    }

    public function getRules(): array    {
      return [
        'fieldvalue' => [
            Required::rule(),
        ],
    ];
}
}
