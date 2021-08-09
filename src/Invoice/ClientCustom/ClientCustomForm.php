<?php

declare(strict_types=1);

namespace App\Invoice\ClientCustom;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;

final class ClientCustomForm extends FormModel
{    
    
    private ?int $client_id=null;
    private ?int $fieldid=null;
    private ?string $fieldvalue='';

    public function getClient_id() : int
    {
      return $this->client_id;
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
