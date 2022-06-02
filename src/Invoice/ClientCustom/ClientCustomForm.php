<?php

declare(strict_types=1);

namespace App\Invoice\ClientCustom;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;

final class ClientCustomForm extends FormModel
{    
    
    private ?int $client_id=null;
    private ?int $custom_field_id=null;
    private ?string $value=null;

    public function getClient_id() : int
    {
      return $this->client_id;
    }

    public function getCustom_field_id() : int
    {
      return $this->custom_field_id;
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
        'value' => [new Required()],
    ];
}
}
