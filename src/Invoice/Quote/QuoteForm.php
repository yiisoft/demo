<?php
declare(strict_types=1);

namespace App\Invoice\Quote;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;

final class QuoteForm extends FormModel
{    
    private string $number ='';
    private ?int $inv_id=null;
    private ?int $group_id=null;
    private ?int $client_id=null;    
    private ?int $status_id=1;
    private ?float $discount_amount=0;
    private ?float $discount_percent=0;
    private ?string $url_key='';
    private ?string $password='';
    private ?string $notes='';    

    public function getInv_id() : int
    {
      return $this->inv_id;
    }

    public function getClient_id() : int
    {
      return $this->client_id;
    }

    public function getGroup_id() : int
    {
      return $this->group_id;
    }

    public function getStatus_id() : int
    {
      return $this->status_id;
    }
        
    public function getNumber() : string
    {
      return $this->number;
    }
    
    public function getDiscount_amount() : float
    {
      return $this->discount_amount;
    }

    public function getDiscount_percent() : float
    {
      return $this->discount_percent;
    }

    public function getUrl_key() : string
    {
      return $this->url_key;
    }

    public function getPassword() : string
    {
      return $this->password;
    }

    public function getNotes() : string
    {
      return $this->notes;
    }

    public function getFormName(): string
    {
      return '';
    }
    
    public function getRules(): array    {
      return [
         'client_id'=> [new Required()],
         'group_id'=> [new Required()],
      ];
     }
}
