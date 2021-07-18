<?php

declare(strict_types=1);

namespace App\Invoice\Product;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;

final class ProductForm extends FormModel
{    
    
    private int $id=null;
    private ?string $product_sku='';
    private ?string $product_name='';
    private string $product_description='';
    private ?float $product_price=null;
    private ?float $purchase_price=null;
    private ?string $provider_name='';
    private ?int $family_id=null;
    private ?int $tax_rate_id=null;
    private ?int $unit_id=null;
    private ?int $product_tariff=null;

    public function getId() : int
    {
      return $this->id;
    }

    public function getProduct_sku() : string
    {
      return $this->product_sku;
    }

    public function getProduct_name() : string
    {
      return $this->product_name;
    }

    public function getProduct_description() : string
    {
      return $this->product_description;
    }

    public function getProduct_price() : float
    {
      return $this->product_price;
    }

    public function getPurchase_price() : float
    {
      return $this->purchase_price;
    }

    public function getProvider_name() : string
    {
      return $this->provider_name;
    }

    public function getFamily_id() : int
    {
      return $this->family_id;
    }

    public function getTax_rate_id() : int
    {
      return $this->tax_rate_id;
    }

    public function getUnit_id() : int
    {
      return $this->unit_id;
    }

    public function getProduct_tariff() : int
    {
      return $this->product_tariff;
    }

    public function getFormName(): string
    {
      return '';
    }

    public function getRules(): array    {
      return [
        'id' => [
            Required::rule(),
        ],
        'product_sku' => [
            Required::rule(),
        ],
        'product_name' => [
            Required::rule(),
        ],
        'product_description' => [
            Required::rule(),
        ],
        'product_price' => [
            Required::rule(),
        ],
        'purchase_price' => [
            Required::rule(),
        ],
        'provider_name' => [
            Required::rule(),
        ],
        'family_id' => [
            Required::rule(),
        ],
        'tax_rate_id' => [
            Required::rule(),
        ],
        'unit_id' => [
            Required::rule(),
        ],
        'product_tariff' => [
            Required::rule(),
        ],
    ];
}
}
