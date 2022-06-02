<?php

declare(strict_types=1);

namespace App\Invoice\Project;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;

final class ProjectForm extends FormModel
{    
    
    private ?int $client_id=null;
    private ?string $name='';

    public function getClient_id() : int
    {
      return $this->client_id;
    }

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
        'client_id' => [new Required()],
        'name' => [new Required()],
    ];
}
}
