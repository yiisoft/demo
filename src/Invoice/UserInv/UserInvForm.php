<?php

declare(strict_types=1);

namespace App\Invoice\UserInv;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;

final class UserInvForm extends FormModel
{       
    private ?int $user_id=null;
    private ?int $type=null;
    private ?bool $active=false;
    private DateTimeImmutable $date_created;
    private DateTimeImmutable $date_modified;
    private ?string $language='';
    private ?string $name='';
    private ?string $company='';
    private ?string $address_1='';
    private ?string $address_2='';
    private ?string $city='';
    private ?string $state='';
    private ?string $zip='';
    private ?string $country='';
    private ?string $phone='';
    private ?string $fax='';
    private ?string $mobile='';
    private ?string $email='';
    private ?string $password='';
    private ?string $web='';
    private ?string $vat_id='';
    private ?string $tax_code='';
    private ?bool $all_clients=false;
    private ?string $salt='';
    private ?string $passwordreset_token='';
    private ?string $subscribernumber='';
    private ?string $iban='';
    private ?int $gln=null;
    private ?string $rcc='';

    public function getUser_id() : int
    {
      return (int)$this->user_id;
    }

    public function getType() : int
    {
      return $this->type;
    }

    public function getActive() : bool
    {
      return $this->active;
    }

    public function getDate_created() : string
    {
      return $this->date_created;
    }

    public function getDate_modified() : string
    {
      return $this->date_modified;
    }

    public function getLanguage() : string
    {
      return $this->language;
    }

    public function getName() : string
    {
      return $this->name;
    }

    public function getCompany() : string
    {
      return $this->company;
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

    public function getMobile() : string
    {
      return $this->mobile;
    }

    public function getEmail() : string
    {
      return $this->email;
    }

    public function getPassword() : string
    {
      return $this->password;
    }

    public function getWeb() : string
    {
      return $this->web;
    }

    public function getVat_id() : string
    {
      return $this->vat_id;
    }

    public function getTax_code() : string
    {
      return $this->tax_code;
    }

    public function getAll_clients() : bool
    {
      return $this->all_clients;
    }

    public function getSalt() : string
    {
      return $this->salt;
    }

    public function getPasswordreset_token() : string
    {
      return $this->passwordreset_token;
    }

    public function getSubscribernumber() : string
    {
      return $this->subscribernumber;
    }

    public function getIban() : string
    {
      return $this->iban;
    }

    public function getGln() : int
    {
      return $this->gln;
    }

    public function getRcc() : string
    {
      return $this->rcc;
    }

    public function getFormName(): string
    {
      return '';
    }

    public function getRules(): array  {
        return [
            'user_id' => [new Required()],
            'type' => [new Required()],
            'language' => [new Required()],
            'name' => [new Required()],
        ];
    }
}
