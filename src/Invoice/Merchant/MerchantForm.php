<?php

declare(strict_types=1);

namespace App\Invoice\Merchant;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;
use \DateTime;
use \DateTimeImmutable;

final class MerchantForm extends FormModel
{    
    
    private ?int $inv_id=null;
    private ?bool $successful=true;
    private ?string $date='';
    private ?string $driver='';
    private ?string $response='';
    private ?string $reference='';

    public function getInv_id() : int
    {
      return $this->inv_id;
    }

    public function getSuccessful() : bool
    {
      return $this->successful;
    }

    public function getDate() : ?\DateTime
    {
       if (isset($this->date) && !empty($this->date)) {
          return new DateTime($this->date);
       }
    }

    public function getDriver() : string
    {
      return $this->driver;
    }

    public function getResponse() : string
    {
      return $this->response;
    }

    public function getReference() : string
    {
      return $this->reference;
    }

    public function getFormName(): string
    {
      return '';
    }

    public function getRules(): array    {
      return [
        'successful' => [
            Required::rule(),
        ],
        'date' => [
            Required::rule(),
        ],
        'driver' => [
            Required::rule(),
        ],
        'response' => [
            Required::rule(),
        ],
        'reference' => [
            Required::rule(),
        ],
    ];
}
}
