<?php

declare(strict_types=1);

namespace App\Invoice\Amount;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;

final class AmountForm extends FormModel
{    
    private ?string $sign = '';
    private ?float $item_sub_total = null;
    private ?float $item_tax_total = null;
    private ?float $tax_total = null;
    private ?float $invoice_total = null;
    private ?float $invoice_paid = null;
    private ?float $invoice_balance = null;
    private ?int $inv_id = null;

    public function getInv_id() : int
    {
      return $this->inv_id;
    }

    public function getSign() : string
    {
      return $this->sign;
    }

    public function getItem_sub_total() : float
    {
      return $this->item_sub_total;
    }

    public function getItem_tax_total() : float
    {
      return $this->item_tax_total;
    }

    public function getTax_total() : float
    {
      return $this->tax_total;
    }

    public function getInvoice_total() : float
    {
      return $this->invoice_total;
    }

    public function getInvoice_paid() : float
    {
      return $this->invoice_paid;
    }

    public function getInvoice_balance() : float
    {
      return $this->invoice_balance;
    }

    public function getFormName(): string
    {
      return '';
    }

    public function getRules(): array    {
      return [
        'sign' => [
            Required::rule(),
        ],
    ];
}
}
