<?php

declare(strict_types=1);

namespace App\Invoice\Inv;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;
use \DateTime;

final class InvForm extends FormModel
{   
    private ?int $group_id=null;
    private ?int $client_id=null;    
    private ?string $password='';
    private ?string $date_created='';
    private ?string $terms='';
    private ?int $payment_method=null;
    
    public function getGroup_id() : int
    {
      return $this->group_id;
    }
    
    public function getClient_id() : int
    {
      return $this->client_id;
    }
    
    public function getPassword() : string
    {
      return $this->password;
    }
    
    public function getDate_created() : ?\DateTime
    {
        if (isset($this->date_created) && !empty($this->date_created)){
            return new DateTime($this->date_created);            
        }
        if (empty($this->date_created)){
            return $this->date_created = null;
        } 
    }
    
    public function getTime_created() : \DateTime
    {
      return $this->time_created = new DateTime(date('H:i:s'));
    }
            
    public function getTerms() : string
    {
      return $this->terms;
    }

    public function getPayment_method() : int
    {
      return $this->payment_method;
    }

    public function getFormName(): string
    {
      return '';
    }

    public function getRules(): array    {
      return [
         'client_id' => [
            Required::rule(),
        ],
        'group_id' => [
            Required::rule(),
        ],
    ];
}
}
