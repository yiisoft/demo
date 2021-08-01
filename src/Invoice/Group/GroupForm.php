<?php

declare(strict_types=1);

namespace App\Invoice\Group;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;

final class GroupForm extends FormModel
{    
    
    private ?string $name='';
    private string $identifier_format='';
    private ?int $next_id=null;
    private ?int $left_pad=null;

    public function getName() : string
    {
      return $this->name;
    }

    public function getIdentifier_format() : string
    {
      return $this->identifier_format;
    }

    public function getNext_id() : int
    {
      return $this->next_id;
    }

    public function getLeft_pad() : int
    {
      return $this->left_pad;
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
        'identifier_format' => [
            Required::rule(),
        ],
        'next_id' => [
            Required::rule(),
        ],
        'left_pad' => [
            Required::rule(),
        ],
    ];
}
}
