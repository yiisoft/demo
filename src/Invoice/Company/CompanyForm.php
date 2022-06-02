<?php

declare(strict_types=1);

namespace App\Invoice\Company;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\Email;
use \DateTimeImmutable;

final class CompanyForm extends FormModel
{    
    private DateTimeImmutable $date_created;
    private DateTimeImmutable $date_modified;
    private ?int $current=0;
    private ?string $name='';
    private ?string $address_1='';
    private ?string $address_2='';
    private ?string $city='';
    private ?string $state='';
    private ?string $zip='';
    private ?string $country='';
    private ?string $phone='';
    private ?string $fax='';
    private ?string $email='';
    private ?string $web='';

    public function getCurrent() : int
    {
      return $this->current;
    }

    public function getName() : string
    {
      return $this->name;
    }

    public function getAddress_1() : string
    {
      return $this->address_1;
    }

    public function getAddress_2() : string
    {
      return $this->address_2;
    }

    public function getCity() : string
    {
      return $this->city;
    }

    public function getState() : string
    {
      return $this->state;
    }

    public function getZip() : string
    {
      return $this->zip;
    }

    public function getCountry() : string
    {
      return $this->country;
    }

    public function getPhone() : string
    {
      return $this->phone;
    }

    public function getFax() : string
    {
      return $this->fax;
    }

    public function getEmail() : string
    {
      return $this->email;
    }

    public function getWeb() : string
    {
      return $this->web;
    }

    public function gettDate_created() : string
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
        'name' => [new Required()],       
        'email' => [new Required()],
    ];
}
}
