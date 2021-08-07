<?php

declare(strict_types=1);

namespace App\Invoice\CustomValue;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;

final class CustomValueForm extends FormModel
{    
    
    private ?int $field=null;
    private ?string $value='';

    public function getField() : int
    {
      return $this->field;
    }

    public function getValue() : string
    {
      return $this->value;
    }

    public function getFormName(): string
    {
      return '';
    }

    public function getRules(): array    {
      return [
        'field' => [
            Required::rule(),
        ],
        'value' => [
            Required::rule(),
        ],
    ];
}
}
