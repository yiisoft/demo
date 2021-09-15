<?php

declare(strict_types=1);

namespace App\Invoice\InvItem;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;
use \DateTime;

final class InvItemForm extends FormModel
{    
    
    private ?int $inv_id=null;
    private ?int $tax_rate_id=null;
    private ?int $product_id=null;
    private ?string $date_added=null;
    private ?int $task_id=null;
    private ?string $name='';
    private ?string $description='';
    private ?float $quantity=null;
    private ?float $price=null;
    private ?float $discount_amount=null;
    private ?int $order=null;
    private ?bool $is_recurring=false;
    private ?string $product_unit='';
    private ?int $unit_id=null;
    private ?string $date=null;

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

    public function getDate_added() : ?\DateTime
    {
      if (isset($this->date_added) && !empty($this->date_added)){
            return new DateTime($this->date_added);  
                      
      }
    }

    public function getTask_id() : int
    {
      return $this->task_id;
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

    public function getIs_recurring() : bool
    {
      return $this->is_recurring;
    }

    public function getProduct_unit() : string
    {
      return $this->product_unit;
    }

    public function getUnit_id() : int
    {
      return $this->unit_id;
    }

    public function getDate() : ?\DateTime
    {
      if (isset($this->date) && !empty($this->date)){
            return new DateTime($this->date);            
      }
      if (empty($this->date)){
            return null;        
      }
    }

    public function getFormName(): string
    {
      return '';
    }

    public function getRules(): array    {
      return [
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
        'is_recurring' => [
            Required::rule(),
        ],
        'product_unit' => [
            Required::rule(),
        ],
        'date' => [
            Required::rule(),
        ],
    ];
}
}
