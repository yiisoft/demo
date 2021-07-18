<?php

declare(strict_types=1); 

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use App\Invoice\TaxRate;
use App\Invoice\Family;
use App\Invoice\Unit;

/**
 * @Entity(
 
 * repository="App\Invoice\Product\ProductRepository",
 * mapper="App\Invoice\Entity\ProductMapper,"
 * constrain="App\Invoice\Entity\Scope\ProductScope"
 * )
 */
 
 class Product
 {
       
   
    /**
     * @BelongsTo(target="TaxRate", nullable=false, fkAction="NO ACTION")
     *
     * @var \Cycle\ORM\Promise\Reference|TaxRate
     */
     private $tax_rate = null;
    

    /**
     * @BelongsTo(target="Family", nullable=false, fkAction="NO ACTION")
     *
     * @var \Cycle\ORM\Promise\Reference|Family
     */
     private $family = null;
    

    /**
     * @BelongsTo(target="Unit", nullable=false, fkAction="NO ACTION")
     *
     * @var \Cycle\ORM\Promise\Reference|Unit
     */
     private $unit = null;
    
    
        /**
     * @Column(type="primary", nullable=false)
     */
     private int $id =  null;
     
    /**
     * @Column(type="text", nullable=true)
     */
     private ?string $product_sku =  '';
     
    /**
     * @Column(type="text", nullable=true)
     */
     private ?string $product_name =  '';
     
    /**
     * @Column(type="longText", nullable=false)
     */
     private string $product_description =  '';
     
    /**
     * @Column(type="decimal", nullable=true)
     */
     private ?float $product_price =  null;
     
    /**
     * @Column(type="decimal", nullable=true)
     */
     private ?float $purchase_price =  null;
     
    /**
     * @Column(type="text", nullable=true)
     */
     private ?string $provider_name =  '';
     
    /**
     * @Column(type="integer", nullable=true)
     */
     private ?int $family_id =  null;
     
    /**
     * @Column(type="integer", nullable=true)
     */
     private ?int $tax_rate_id =  null;
     
    /**
     * @Column(type="integer", nullable=true)
     */
     private ?int $unit_id =  null;
     
    /**
     * @Column(type="integer", nullable=true)
     */
     private ?int $product_tariff =  null;
     
     public function __construct(
          int $id = null,
         string $product_sku = '',
         string $product_name = '',
         string $product_description = '',
         float $product_price = null,
         float $purchase_price = null,
         string $provider_name = '',
         int $family_id = null,
         int $tax_rate_id = null,
         int $unit_id = null,
         int $product_tariff = null
     )
     {
         $this->id=$id;
         $this->product_sku=$product_sku;
         $this->product_name=$product_name;
         $this->product_description=$product_description;
         $this->product_price=$product_price;
         $this->purchase_price=$purchase_price;
         $this->provider_name=$provider_name;
         $this->family_id=$family_id;
         $this->tax_rate_id=$tax_rate_id;
         $this->unit_id=$unit_id;
         $this->product_tariff=$product_tariff;
     }
    
    public function getTaxRate() : ?TaxRate {
      return $this->tax_rate;
    }
    
    public function getFamily() : ?Family {
      return $this->family;
    }
    
    public function getUnit() : ?Unit {
      return $this->unit;
    }
    
    public function getId(): int
    {
      return $this->id;
    }
    
    public function setId(int $id) : void
    {
      $this->id =  $id;
    }
    
    public function getProduct_sku(): ?string
    {
      return $this->product_sku;
    }
    
    public function setProduct_sku(string $product_sku) : void
    {
      $this->product_sku =  $product_sku;
    }
    
    public function getProduct_name(): ?string
    {
      return $this->product_name;
    }
    
    public function setProduct_name(string $product_name) : void
    {
      $this->product_name =  $product_name;
    }
    
    public function getProduct_description(): string
    {
      return $this->product_description;
    }
    
    public function setProduct_description(string $product_description) : void
    {
      $this->product_description =  $product_description;
    }
    
    public function getProduct_price(): ?float
    {
      return $this->product_price;
    }
    
    public function setProduct_price(float $product_price) : void
    {
      $this->product_price =  $product_price;
    }
    
    public function getPurchase_price(): ?float
    {
      return $this->purchase_price;
    }
    
    public function setPurchase_price(float $purchase_price) : void
    {
      $this->purchase_price =  $purchase_price;
    }
    
    public function getProvider_name(): ?string
    {
      return $this->provider_name;
    }
    
    public function setProvider_name(string $provider_name) : void
    {
      $this->provider_name =  $provider_name;
    }
    
    public function getFamily_id(): ?int
    {
      return $this->family_id;
    }
    
    public function setFamily_id(int $family_id) : void
    {
      $this->family_id =  $family_id;
    }
    
    public function getTax_rate_id(): ?int
    {
      return $this->tax_rate_id;
    }
    
    public function setTax_rate_id(int $tax_rate_id) : void
    {
      $this->tax_rate_id =  $tax_rate_id;
    }
    
    public function getUnit_id(): ?int
    {
      return $this->unit_id;
    }
    
    public function setUnit_id(int $unit_id) : void
    {
      $this->unit_id =  $unit_id;
    }
    
    public function getProduct_tariff(): ?int
    {
      return $this->product_tariff;
    }
    
    public function setProduct_tariff(int $product_tariff) : void
    {
      $this->product_tariff =  $product_tariff;
    }
}
                     
    
