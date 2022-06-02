<?php

declare(strict_types=1);

namespace App\Invoice\UserClient;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;

final class UserClientForm extends FormModel
{    
    
    private ?int $user_id=null;
    private ?int $client_id=null;

    public function getUser_id() : int
    {
      return $this->user_id;
    }

    public function getClient_id() : int
    {
      return $this->client_id;
    }

    public function getFormName(): string
    {
      return '';
    }

    public function getRules(): array    {
      return [
    ];
}
}
