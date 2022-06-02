<?php

declare(strict_types=1);

namespace App\Invoice\CompanyPrivate;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;

final class CompanyPrivateForm extends FormModel
{        
    private DateTimeImmutable $date_created;
    private DateTimeImmutable $date_modified;
    private ?int $company_id=null;
    private ?string $vat_id='';
    private ?string $tax_code='';
    private ?string $iban='';
    private ?int $gln=null;
    private ?string $rcc='';

    public function getCompany_id() : int
    {
      return $this->company_id;
    }

    public function getVat_id() : string
    {
      return $this->vat_id;
    }

    public function getTax_code() : string
    {
      return $this->tax_code;
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

    public function getRules(): array 
    {
      return [ 
        'company_id' => [new Required()],
      ];
}
}
