<?php

declare(strict_types=1); 

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use App\Invoice\Entity\Inv;
use App\Invoice\Entity\TaxRate;
use App\Invoice\Entity\Product;
use App\Invoice\Entity\Unit;
use App\Invoice\Entity\Task;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use DateTime;
use DateTimeImmutable;
  
 /**
 * @Entity(
 * repository="App\Invoice\Item\ItemRepository"
 * )
 */
 
 class Item
 {
    /**
     * @BelongsTo(target="Inv", nullable=false)
     *
     * @var \Cycle\ORM\Promise\Reference|Inv
     */
     private $inv = null;
    

    /**
     * @BelongsTo(target="TaxRate", nullable=false)
     *
     * @var \Cycle\ORM\Promise\Reference|TaxRate
     */
     private $tax_rate = null;
    

    /**
     * @BelongsTo(target="Product", nullable=false)
     *
     * @var \Cycle\ORM\Promise\Reference|Product
     */
     private $product = null;
    

    /**
     * @BelongsTo(target="Unit", nullable=false)
     *
     * @var \Cycle\ORM\Promise\Reference|Unit
     */
     private $unit = null;
    

    /**
     * @BelongsTo(target="Task", nullable=false)
     *
     * @var \Cycle\ORM\Promise\Reference|Task
     */
     private $task = null;
    
    
    /**
     * @Column(type="primary")
     */
     public ?int $id =  null;
     
    /**
     * @Column(type="integer(11)", nullable=false)
     */
     private ?int $inv_id =  null;
     
    /**
     * @Column(type="integer(11)", nullable=false,default=0)
     */
     private ?int $tax_rate_id =  null;
     
    /**
     * @Column(type="integer(11)", nullable=true)
     */
     private ?int $product_id =  null;
     
    /**
     * @Column(type="date", nullable=false)
     */
     private $date_added =  '';
     
    /**
     * @Column(type="integer(11)", nullable=true)
     */
     private ?int $task_id =  null;
     
    /**
     * @Column(type="text", nullable=true)
     */
     private ?string $name =  '';
     
    /**
     * @Column(type="longText", nullable=true)
     */
     private ?string $description =  '';
     
    /**
     * @Column(type="decimal(10,2)", nullable=false)
     */
     private ?float $quantity =  null;
     
    /**
     * @Column(type="decimal(20,2)", nullable=true)
     */
     private ?float $price =  null;
     
    /**
     * @Column(type="decimal(20,2)", nullable=true)
     */
     private ?float $discount_amount =  null;
     
    /**
     * @Column(type="integer(2)", nullable=false,default=0)
     */
     private ?int $order =  null;
     
    /**
     * @Column(type="boolean",nullable=true)
     */
     private ?bool $is_recurring =  false;
     
    /**
     * @Column(type="integer(11)", nullable=true)
     */
     private ?int $unit_id =  null;
     
     /**
     * @Column(type="string(50)", nullable=true)
     */
     private ?string $product_unit =  null;
          
    /**
     * @Column(type="date", nullable=true)
     */
     private $date =  '';
     
     public function __construct(
         int $id = null,
         int $inv_id = null,
         int $tax_rate_id = null,
         int $product_id = null,
         $date_added = '',
         int $task_id = null,
         string $name = '',
         string $description = '',
         float $quantity = null,
         float $price = null,
         float $discount_amount = null,
         int $order = null,
         bool $is_recurring = false,
         string $product_unit = '',
         int $unit_id = null,
         $date = ''
     )
     {
         $this->id=$id;
         $this->inv_id=$inv_id;
         $this->tax_rate_id=$tax_rate_id;
         $this->product_id=$product_id;
         $this->date_added=$date_added;
         $this->task_id=$task_id;
         $this->name=$name;
         $this->description=$description;
         $this->quantity=$quantity;
         $this->price=$price;
         $this->discount_amount=$discount_amount;
         $this->order=$order;
         $this->is_recurring=$is_recurring;
         $this->product_unit=$product_unit;
         $this->unit_id=$unit_id;
         $this->date=$date;
    }
     
    public function getId(): string
    {
     return (string)$this->id;
    }
    
    public function setId(int $id) : void
    {
      $this->id =  $id;
    }
    
    public function getInv() : ?Inv {
      return $this->inv;
    }
    
    public function getTaxRate() : ?TaxRate {
      return $this->tax_rate;
    }
    
    public function getProduct() : ?Product {
      return $this->product;
    }
    
    public function getUnit() : ?Unit {
      return $this->unit;
    }
    
    public function getTask() : ?Task {
      return $this->task;
    }
    
    public function getInv_id(): string
    {
     return (string)$this->inv_id;
    }
    
    public function setInv_id(int $inv_id) : void
    {
      $this->inv_id =  $inv_id;
    }
    
    public function getTax_rate_id(): string
    {
     return (string)$this->tax_rate_id;
    }
    
    public function setTax_rate_id(int $tax_rate_id) : void
    {
      $this->tax_rate_id =  $tax_rate_id;
    }
    
    public function getProduct_id(): ?string
    {
     return (string)$this->product_id;
    }
    
    public function setProduct_id(int $product_id) : void
    {
      $this->product_id =  $product_id;
    }
        
    public function getDate_added() : ?DateTimeImmutable  
    {
        if (isset($this->date_added) && !empty($this->date_added)){
            return $this->date_added;            
        }
        
        if (empty($this->date_added)){
            return $this->date_added = null;
        }
    }    
    
    public function setDate_added(DateTime $date_added): void
    {
        $this->date_added = $date_added->format('Y-m-d');        
    }
    
    public function getTask_id(): ?string
    {
     return (string)$this->task_id;
    }
    
    public function setTask_id(int $task_id) : void
    {
      $this->task_id =  $task_id;
    }
    
    public function getName(): ?string
    {
     return $this->name;
    }
    
    public function setName(string $name) : void
    {
      $this->name =  $name;
    }
    
    public function getDescription(): ?string
    {
     return $this->description;
    }
    
    public function setDescription(string $description) : void
    {
      $this->description =  $description;
    }
    
    public function getQuantity(): float
    {
     return $this->quantity;
    }
    
    public function setQuantity(float $quantity) : void
    {
      $this->quantity =  $quantity;
    }
    
    public function getPrice(): ?float
    {
     return $this->price;
    }
    
    public function setPrice(float $price) : void
    {
      $this->price =  $price;
    }
    
    public function getDiscount_amount(): ?float
    {
     return $this->discount_amount;
    }
    
    public function setDiscount_amount(float $discount_amount) : void
    {
      $this->discount_amount =  $discount_amount;
    }
    
    public function getOrder(): int
    {
     return $this->order;
    }
    
    public function setOrder(int $order) : void
    {
      $this->order =  $order;
    }
    
    public function getIs_recurring(): ?bool
    {
     return $this->is_recurring;
    }
    
    public function setIs_recurring(bool $is_recurring) : void
    {
      $this->is_recurring =  $is_recurring;
    }
        
    public function getUnit_id(): ?string
    {
     return (string)$this->unit_id;
    }
    
    public function setUnit_id(int $unit_id) : void
    {
      $this->unit_id =  $unit_id;
    }
    
    public function getDate() : ?DateTimeImmutable  
    {
        if (isset($this->date) && !empty($this->date)){
            return $this->date;            
        }
        if (!empty($this->date)) {
            return $this->date = null;            
        }
    }    
    
    public function setDate(?DateTime $date): void
    {
        $this->date = $date->format('Y-m-d');
    }
    
    public function getProduct_unit(): ?string
    {
     return $this->product_unit;
    }
    
    public function setProduct_unit(string $product_unit) : void
    {
      $this->product_unit =  $product_unit;
    }
    
    public function isNewRecord(): bool
    {
        return $this->getId() === null;
    }
}