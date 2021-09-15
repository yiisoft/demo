<?php

declare(strict_types=1);

namespace App\Invoice\PaymentMethod;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;

final class PaymentMethodForm extends FormModel
{    
    
    private ?string $name='';

    public function getName() : string
    {
      return $this->name;
    }

    public function getFormName(): string
    {
      return '';
    }

    public function getRules(): array    {
      return [
        'name' => [
            Required::rule(),
        ],
    ];
}
}
