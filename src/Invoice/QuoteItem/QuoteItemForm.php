<?php

declare(strict_types=1);

namespace App\Invoice\QuoteItem;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;
use \DateTime;
use \DateTimeImmutable;

final class QuoteItemForm extends FormModel
{    
    
    private ?int $quote_id=null;
    private ?int $tax_rate_id=null;
    private ?int $product_id=null;
    private ?string $date_added='';
    private ?string $name='';
    private ?string $description='';
    private ?float $quantity=null;
    private ?float $price=null;
    private ?float $discount_amount=null;
    private ?int $order=null;
    private ?string $product_unit='';
    private ?int $product_unit_id=null;

    public function getQuote_id() : int
    {
      return $this->quote_id;
    }

    public function getTax_rate_id() : int
    {
      return $this->tax_rate_id;
    }

    public function getProduct_id() : int
    {
      return $this->product_id;
    }

    public function getDate_added() : ?\DateTime
    {
       if (isset($this->date_added) && !empty($this->date_added)) {
          return new DateTime($this->date_added);
       }
    }

    public function getName() : string
    {
      return $this->name;
    }

    public function getDescription() : string
    {
      return $this->description;
    }

    public function getQuantity() : float
    {
      return $this->quantity;
    }

    public function getPrice() : float
    {
      return $this->price;
    }

    public function getDiscount_amount() : float
    {
      return $this->discount_amount;
    }

    public function getOrder() : int
    {
      return $this->order;
    }

    public function getProduct_unit() : string
    {
      return $this->product_unit;
    }

    public function getProduct_unit_id() : int
    {
      return $this->product_unit_id;
    }

    public function getFormName(): string
    {
      return '';
    }

    public function getRules(): array    {
      return [
        'tax_rate_id' => [
            Required::rule(),
        ],          
        'product_id' => [
            Required::rule(),
        ],          
        'quote_id' => [
            Required::rule(),
        ],  
        'date_added' => [
            Required::rule(),
        ],
        'name' => [
            Required::rule(),
        ],
        'description' => [
            Required::rule(),
        ],
        'quantity' => [
            Required::rule(),
        ],
        'price' => [
            Required::rule(),
        ],
        'discount_amount' => [
            Required::rule(),
        ],
        'order' => [
            Required::rule(),
        ],
        'product_unit' => [
            Required::rule(),
        ],
        'product_unit_id' => [
            Required::rule(),
        ],
    ];
}
}
