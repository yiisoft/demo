<?php

declare(strict_types=1);

namespace App\Invoice\Client;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;
use \DateTimeImmutable;
use \DateTime;

final class ClientForm extends FormModel
{
    private DateTimeImmutable $client_date_created;
    private DateTimeImmutable $client_date_modified;
    private ?string $client_name='';
    private ?string $client_address_1='';
    private ?string $client_address_2='';
    private ?string $client_city='';
    private ?string $client_state='';
    private ?string $client_zip='';
    private ?string $client_country='';
    private ?string $client_phone='';
    private ?string $client_fax='';
    private ?string $client_mobile='';
    private ?string $client_email='';
    private ?string $client_web='';
    private ?string $client_vat_id='';
    private ?string $client_tax_code='';
    private ?string $client_language='';
    private ?bool $client_active=false;
    private ?string $client_surname='';
    private ?string $client_avs='';
    private ?string $client_insurednumber='';
    private ?string $client_veka='';
    private ?string $client_birthdate='';
    private ?int $client_gender=null;

    public function getClient_date_created() : string
    {
      return $this->client_date_created;
    }

    public function getClient_date_modified() : string
    {
      return $this->client_date_modified;
    }

    public function getClient_name() : string
    {
      return $this->client_name;
    }

    public function getClient_address_1() : string
    {
      return $this->client_address_1;
    }

    public function getClient_address_2() : string
    {
      return $this->client_address_2;
    }

    public function getClient_city() : string
    {
      return $this->client_city;
    }

    public function getClient_state() : string
    {
      return $this->client_state;
    }

    public function getClient_zip() : string
    {
      return $this->client_zip;
    }

    public function getClient_country() : string
    {
      return $this->client_country;
    }

    public function getClient_phone() : string
    {
      return $this->client_phone;
    }

    public function getClient_fax() : string
    {
      return $this->client_fax;
    }

    public function getClient_mobile() : string
    {
      return $this->client_mobile;
    }

    public function getClient_email() : string
    {
      return $this->client_email;
    }

    public function getClient_web() : string
    {
      return $this->client_web;
    }

    public function getClient_vat_id() : string
    {
      return $this->client_vat_id;
    }

    public function getClient_tax_code() : string
    {
      return $this->client_tax_code;
    }

    public function getClient_language() : string
    {
      return $this->client_language;
    }

    public function getClient_active() : bool
    {
      return $this->client_active;
    }

    public function getClient_surname() : string
    {
      return $this->client_surname;
    }

    public function getClient_avs() : string
    {
      return $this->client_avs;
    }

    public function getClient_insurednumber() : string
    {
      return $this->client_insurednumber;
    }

    public function getClient_veka() : string
    {
      return $this->client_veka;
    }

    public function getClient_birthdate() : ?\DateTime
    {
       if (isset($this->client_birthdate) && !empty($this->client_birthdate)) {
          return new DateTime($this->client_birthdate);
       }
       if (empty($this->client_birthdate)){
          return null;
        }
    }

    public function getClient_gender() : int
    {
      return $this->client_gender;
    }

    public function getFormName(): string
    {
      return '';
    }

    public function getRules(): array    {
      return [
        'client_name' => [
            Required::rule(),
        ],
        'client_email' => [
            Required::rule(),
        ],
    ];
}
}
