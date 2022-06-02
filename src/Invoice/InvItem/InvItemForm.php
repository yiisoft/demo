<?php

declare(strict_types=1);

namespace App\Invoice\InvItem;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;
use DateTime;

final class InvItemForm extends FormModel
{        
    private ?int $inv_id=null;
    private ?int $tax_rate_id=null;
    private ?int $product_id=null;
    private ?string $name='';
    private ?string $description='';
    private ?float $quantity=null;
    private ?float $price=null;
    private ?float $discount_amount=null;
    private ?int $order=null;
    private ?string $product_unit='';
    private ?int $product_unit_id=null;
    private ?string $date='';
            
    public function getDate() : ?\DateTime
    {
        return new DateTime($this->date);
    }

    public function getInv_id() : int
    {
      return $this->inv_id;
    }

    public function getTax_rate_id() : int
    {
      return $this->tax_rate_id;
    }

    public function getProduct_id() : int
    {
      return $this->product_id;
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
    
    public function getRule(): array    {
      return [
        'tax_rate_id' => [new Required()],
        'product_id' => [new Required()],
        'quantity' => [new Required()],
        'price' => [new Required()],
        'discount_amount' => [new Required()],
        'order' => [new Required()],
        'product_unit_id' => [new Required()],
    ];
    }
}
