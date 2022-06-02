<?php

declare(strict_types=1);

namespace App\Invoice\Profile;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\Email;
use \DateTime;
use \DateTimeImmutable;

final class ProfileForm extends FormModel
{    
    
    private ?int $company_id=null;
    private ?int $current=0;
    private ?string $mobile='';
    private ?string $email='';
    private ?string $description='';
    private ?string $date_created='';
    private ?string $date_modified='';

    public function getCompany_id() : int
    {
      return $this->company_id;
    }

    public function getCurrent() : int
    {
      return $this->current;
    }

    public function getMobile() : string
    {
      return $this->mobile;
    }

    public function getEmail() : string
    {
      return $this->email;
    }
    
    public function getDescription() : string
    {
      return $this->description;
    }

    public function getDate_created() : string
    {
      return $this->date_created;
    }

    public function getDate_modified() : string
    {
      return $this->date_modified;
    }

    public function getFormName(): string
    {
      return '';
    }

    public function getRules(): array    {
      return [
        'mobile' => [new Required()],
        'email' => [new Required(),
            new Email(),
        ],
        'description' => [new Required()],
    ];
}
}
