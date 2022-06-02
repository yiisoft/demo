<?php
declare(strict_types=1); 

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use DateTime;
use DateTimeImmutable;
use App\Invoice\Entity\TaxRate;
use App\Invoice\Entity\Product;
use App\Invoice\Entity\Quote;

#[Entity(repository: \App\Invoice\QuoteItem\QuoteItemRepository::class)]
class QuoteItem
{    
    #[BelongsTo(target:\App\Invoice\Entity\TaxRate::class, nullable: false, fkAction: 'NO ACTION')]
    private ?TaxRate $tax_rate = null;
    
    #[BelongsTo(target:\App\Invoice\Entity\Product::class, nullable: false, fkAction: 'NO ACTION')]
    private ?Product $product = null;
    
    #[BelongsTo(target:\App\Invoice\Entity\Quote::class, nullable: false, fkAction: 'NO ACTION')]
    private ?Quote $quote = null;
        
    #[Column(type: 'primary')]
    public ?int $id =  null;
     
    #[Column(type: 'integer(11)', nullable: false)]
    private ?int $quote_id =  null;
    
    // Mandatory: The item MUST have a tax rate even if it is a zero rate
    #[Column(type: 'integer(11)', nullable: false)]
    private int $tax_rate_id;
    
    // Mandatory: The item MUST have a product
    #[Column(type: 'integer(11)', nullable: false)]
    private int $product_id;
     
    #[Column(type: 'date', nullable: false)]
    private $date_added;
     
    #[Column(type: 'text', nullable: true)]
    private ?string $name =  '';
     
    #[Column(type: 'text', nullable: true)]
    private ?string $description =  '';
    
    #[Column(type: 'decimal(20,2)', nullable: false, default: 1.00)]
    private ?float $quantity = 1.00;
     
    #[Column(type: 'decimal(20,2)', nullable: false, default: 0.00)]
    private ?float $price =  0.00;
     
    #[Column(type: 'decimal(20,2)', nullable: false, default: 0.00)]
    private ?float $discount_amount = 0.00;
    
    #[Column(type: 'integer(2)', nullable: false, default:0)]
    private ?int $order =  null;
    
    #[Column(type: 'string(50)', nullable: true)]
    private ?string $product_unit =  '';
    
    #[Column(type: 'integer(11)', nullable: false)]
    private int $product_unit_id;
     
    public function __construct(
        int $id = null,
        int $quote_id = null,
        string $name = '',
        string $description = '',
        float $quantity = 1.00,
        float $price = 0.00,
        float $discount_amount = 0.00,
        int $order = null,
        string $product_unit = ''
    )
    {
        $this->id=$id;
        $this->quote_id=$quote_id;
        $this->date_added= new DateTimeImmutable();
        $this->name=$name;
        $this->description=$description;
        $this->quantity=$quantity;
        $this->price=$price;
        $this->discount_amount=$discount_amount;
        $this->order=$order;
        $this->product_unit=$product_unit;
    }
    
    public function getTaxRate() : TaxRate
    {
      return $this->tax_rate;
    }
    
    public function getProduct() : Product
    {
      return $this->product;
    }
    
    public function getQuote() : Quote
    {
      return $this->quote;
    }
    
    public function getId(): string
    {
     return (string)$this->id;
    }
    
    public function setId(int $id) : void
    {
      $this->id = $id;
    }
    
    public function getQuote_id(): string
    {
     return (string)$this->quote_id;
    }
    
    public function setQuote_id(int $quote_id) : void
    {
      $this->quote_id =  $quote_id;
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
    
    public function getQuantity(): ?float
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
}