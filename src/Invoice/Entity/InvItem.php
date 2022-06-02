<?php
declare(strict_types=1); 

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use App\Invoice\Entity\Inv;
use App\Invoice\Entity\TaxRate;
use App\Invoice\Entity\Product;
use App\Invoice\Entity\Task;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use DateTime;
use DateTimeImmutable;
  
#[Entity(repository: \App\Invoice\InvItem\InvItemRepository::class)]
class InvItem
{    
    #[BelongsTo(target: \App\Invoice\Entity\TaxRate::class, nullable: false, fkAction: 'NO ACTION')]
    private ?TaxRate $tax_rate = null;
    
    #[BelongsTo(target: \App\Invoice\Entity\Product::class, nullable: false, fkAction: 'NO ACTION')]
    private ?Product $product = null;
    
    #[BelongsTo(target: \App\Invoice\Entity\Inv::class, nullable: false, fkAction: 'NO ACTION')]
    private ?Inv $inv = null;    
    
    #[Column(type: 'primary')]
    public ?int $id =  null;
     
    #[Column(type: 'integer(11)', nullable: false)]
    private ?int $inv_id =  null;
     
    #[Column(type: 'integer(11)', nullable: false, default:0)]
    private ?int $tax_rate_id =  null;
     
    #[Column(type: 'integer(11)', nullable: true)]
    private ?int $product_id =  null;
    
    #[Column(type: 'date', nullable: false)]
    private $date_added;
     
    #[Column(type: 'integer(11)', nullable: true)]
    private ?int $task_id =  null;
     
    #[Column(type: 'text', nullable: true)]
    private ?string $name =  '';
     
    #[Column(type: 'longText', nullable: true)]
    private ?string $description =  '';
    
    #[Column(type: 'decimal(10,2)', nullable: false, default: 1)]
    private ?float $quantity =  null;
     
    #[Column(type: 'decimal(20,2)', nullable: false, default: 0.00)]
    private ?float $price =  0.00;
     
    #[Column(type: 'decimal(20,2)', nullable: false, default: 0.00)]
    private ?float $discount_amount =  0.00;
    
    #[Column(type: 'integer(2)', nullable: false, default:0)]
    private ?int $order =  null;
     
    #[Column(type: 'boolean', nullable: true)]
    private ?bool $is_recurring =  false;
     
   #[Column(type: 'string(50)', nullable: true)]
    private ?string $product_unit =  '';
    
    #[Column(type: 'integer(11)', nullable: true)]
    private ?int $product_unit_id =  null;
    
    #[Column(type: 'date', nullable: true)]
    private $date =  '';
     
    public function __construct(
        int $id = null,
        int $inv_id = null,
        int $tax_rate_id = null,
        int $product_id = null,
        int $task_id = null,
        string $name = '',
        string $description = '',
        float $quantity = null,
        float $price = null,
        float $discount_amount = null,
        int $order = null,
        bool $is_recurring = false,
        string $product_unit = '',
        int $product_unit_id = null,
        $date = ''
    )
    {
        $this->id=$id;
        $this->inv_id=$inv_id;
        $this->tax_rate_id=$tax_rate_id;
        $this->product_id=$product_id;
        $this->date_added=new DateTimeImmutable();
        $this->task_id=$task_id;
        $this->name=$name;
        $this->description=$description;
        $this->quantity=$quantity;
        $this->price=$price;
        $this->discount_amount=$discount_amount;
        $this->order=$order;
        $this->is_recurring=$is_recurring;
        $this->product_unit=$product_unit;
        $this->product_unit_id=$product_unit_id;
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
    
    public function getTaxRate() : TaxRate {
      return $this->tax_rate;
    }
    
    public function getProduct() : Product {
      return $this->product;
    } 
    
    public function getInv() : Inv {
      return $this->inv;
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
        
     public function getDate_added(): DateTimeImmutable
    {
      return $this->date_added;
    }
    
    public function setDate_added(DateTime $date_added) : void
    {
      $this->date_added =  $date_added;
    }
    
    public function getTask_id(): ?string
    {
     return (string)$this->task_id;
    }
    
    public function setTask_id(string $task_id) : void
    {
      $this->task_id = $task_id;
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
    
    public function getDate() : ?DateTimeImmutable  
    {
        if (isset($this->date) && !empty($this->date)){
            return $this->date;            
        }
        if (empty($this->date)){
            return $this->date = null;
        } 
    }    
    
    public function setDate(?DateTime $date): void
    {
      $this->date = $date;
    }
    
    public function getProduct_unit(): ?string
    {
       return $this->product_unit;
    }
    
    public function setProduct_unit(string $product_unit) : void
    {
      $this->product_unit =  $product_unit;
    }
    
    public function getProduct_unit_id(): ?string
    {
     return (string)$this->product_unit_id;
    }
    
    public function setProduct_unit_id(int $product_unit_id) : void
    {
      $this->product_unit_id =  $product_unit_id;
    }
    
    public function isNewRecord(): bool
    {
        return $this->getId() === null;
    }
}