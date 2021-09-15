<?php

declare(strict_types=1);

namespace App\Invoice\QuoteTaxRate;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;

final class QuoteTaxRateForm extends FormModel
{    
    
    private ?int $quote_id=null;
    private ?int $tax_rate_id=null;
    private ?int $include_item_tax=null;
    private ?float $quote_tax_rate_amount=null;

    public function getQuote_id() : int
    {
      return $this->quote_id;
    }

    public function getTax_rate_id() : int
    {
      return $this->tax_rate_id;
    }

    public function getInclude_item_tax() : int
    {
      return $this->include_item_tax;
    }

    public function getQuote_tax_rate_amount() : float
    {
      return $this->quote_tax_rate_amount;
    }

    public function getFormName(): string
    {
      return '';
    }

    public function getRules(): array    {
      return [
        'include_item_tax' => [
            Required::rule(),
        ],
        'quote_tax_rate_amount' => [
            Required::rule(),
        ],
    ];
}
}
