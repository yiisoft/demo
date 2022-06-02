<?php

declare(strict_types=1);

namespace App\Invoice\CustomField;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;

final class CustomFieldForm extends FormModel
{    
    
    private ?string $table='';
    private ?string $label='';
    private ?string $type='';
    private ?int $location=null;
    private ?int $order=null;

    public function getTable() : string
    {
      return $this->table;
    }

    public function getLabel() : string
    {
      return $this->label;
    }

    public function getType() : string
    {
      return $this->type;
    }

    public function getLocation() : int
    {
      return $this->location;
    }

    public function getOrder() : int
    {
      return $this->order;
    }

    public function getFormName(): string
    {
      return '';
    }

    public function getRules(): array    {
      return [
        'table' => [new Required()],
        'label' => [new Required()],
        'type' => [new Required()],
        'location' => [new Required()],
        'order' => [new Required()],
    ];
}
}
