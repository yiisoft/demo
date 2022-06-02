<?php
declare(strict_types=1);

namespace App\Invoice\InvAmount;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;

final class InvAmountForm extends FormModel
{    
    
    private ?int $inv_id=null;
    private ?float $item_subtotal=null;
    private ?float $item_tax_total=null;
    private ?float $tax_total=null;
    private ?float $total=null;
    private ?float $paid=null;
    private ?float $balance=null;

    public function getInv_id() : int
    {
      return $this->inv_id;
    }

    public function getItem_subtotal() : float
    {
      return $this->item_subtotal;
    }

    public function getItem_tax_total() : float
    {
      return $this->item_tax_total;
    }

    public function getTax_total() : float
    {
      return $this->tax_total;
    }

    public function getTotal() : float
    {
      return $this->total;
    }
    
    public function getPaid() : float
    {
      return $this->paid;
    }
    
    public function getBalance() : float
    {
      return $this->balance;
    }

    public function getFormName(): string
    {
      return '';
    }

    public function getRules(): array    {
      return [
        'item_subtotal' => [new Required()],
        'item_tax_total' => [new Required()],
        'tax_total' => [new Required()],
        'total' => [new Required()],
        'inv_id' => [new Required()],
    ];
}
}
