<?php

declare(strict_types=1);

namespace App\Invoice\QuoteItemAmount;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;

final class QuoteItemAmountForm extends FormModel
{    
    
    private ?int $quote_item_id=null;
    private ?float $subtotal=null;
    private ?float $tax_total=null;
    private ?float $discount=null;
    private ?float $total=null;

    public function getQuote_item_id() : int
    {
      return $this->quote_item_id;
    }

    public function getSubtotal() : float
    {
      return $this->subtotal;
    }

    public function getTax_total() : float
    {
      return $this->tax_total;
    }

    public function getDiscount() : float
    {
      return $this->discount;
    }

    public function getTotal() : float
    {
      return $this->total;
    }

    public function getFormName(): string
    {
      return '';
    }

    public function getRules(): array    {
      return [
        'subtotal' => [new Required()],
        'tax_total' => [new Required()],
        'discount' => [new Required()],
        'total' => [new Required()],
    ];
}
}
