<?php

declare(strict_types=1);

namespace App\Invoice\ItemLookup;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;

final class ItemLookupForm extends FormModel
{    
    
    private ?string $name='';
    private ?string $description='';
    private ?float $price=null;

    public function getName() : string
    {
      return $this->name;
    }

    public function getDescription() : string
    {
      return $this->description;
    }

    public function getPrice() : float
    {
      return $this->price;
    }

    public function getFormName(): string
    {
      return '';
    }

    public function getRules(): array    {
      return [
        'name' => [new Required()],
        'description' => [new Required()],
        'price' => [new Required()],
    ];
}
}
