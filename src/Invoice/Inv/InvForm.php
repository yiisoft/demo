<?php
declare(strict_types=1);

namespace App\Invoice\Inv;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;

final class InvForm extends FormModel
{    
    private string $number ='';
    private ?int $quote_id=null;
    private ?int $group_id=null;
    private ?int $client_id=null;    
    private ?int $creditinvoice_parent_id=null;
    private ?int $status_id=1;
    private ?float $discount_amount=0;
    private ?float $discount_percent=0;
    private ?string $url_key='';
    private ?string $password='';
    private ?int $payment_method=0;
    private ?string $terms='';    

    public function getQuote_id() : int
    {
      return $this->quote_id;
    }

    public function getClient_id() : int
    {
      return $this->client_id;
    }

    public function getGroup_id() : int
    {
      return $this->group_id;
    }
    
    public function getCreditinvoice_parent_id() : int
    {
      return $this->creditinvoice_parent_id;
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
    
    public function getPayment_method() : int
    {
      return $this->payment_method;
    }

    public function getTerms() : string
    {
      return $this->terms;
    }

    public function getFormName(): string
    {
      return '';
    }
    
    public function getRules(): array    {
      return [
        'client_id' => [new Required()],
        'group_id' => [new Required()],
      ];
    }
}
