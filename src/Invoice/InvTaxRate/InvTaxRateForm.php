<?php
declare(strict_types=1);

namespace App\Invoice\InvTaxRate;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;

final class InvTaxRateForm extends FormModel
{        
    private ?int $inv_id=null;
    private ?int $tax_rate_id=null;
    private ?int $include_item_tax=null;
    private ?float $inv_tax_rate_amount=null;

    public function getInv_id() : int
    {
      return $this->inv_id;
    }

    public function getTax_rate_id() : int
    {
      return $this->tax_rate_id;
    }

    public function getInclude_item_tax() : int
    {
      return $this->include_item_tax;
    }

    public function getInv_tax_rate_amount() : float
    {
      return $this->inv_tax_rate_amount;
    }

    public function getFormName(): string
    {
      return '';
    }

    public function getRules(): array    {
      return [
        'include_item_tax' => [new Required()],
        'inv_tax_rate_amount' => [new Required()],
    ];
}
}
