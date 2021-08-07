<?php

declare(strict_types=1);

namespace App\Invoice\Invcust;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;

final class InvcustForm extends FormModel
{    
    
    private ?int $inv_id=null;
    private ?int $fieldid=null;
    private ?string $fieldvalue='';

    public function getInv_id() : int
    {
      return $this->inv_id;
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
