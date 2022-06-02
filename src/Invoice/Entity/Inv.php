<?php
declare(strict_types=1); 

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use Cycle\ORM\Entity\Behavior;
use App\User\User;
use App\Invoice\Entity\Group;
use App\Invoice\Entity\Client;
use DateTimeImmutable;

#[Entity(repository: \App\Invoice\Inv\InvRepository::class)]
#[Behavior\CreatedAt(field: 'date_created', column: 'date_created')] 
#[Behavior\UpdatedAt(field: 'date_modified', column: 'date_modified')]
class Inv
{
    #[BelongsTo(target:User::class, nullable: false)]
    private ?User $user = null;
    
    #[BelongsTo(target:Group::class, nullable: true, fkAction:'NO ACTION')]
    private ?Group $group = null;
    
    #[BelongsTo(target:Client::class, nullable: true, fkAction:'NO ACTION')]
    private ?Client $client = null;
     
    #[Column(type: 'primary')]
    private ?int $id =  null;
     
    #[Column(type: 'integer(11)', nullable: false)]
    private ?int $client_id =  null;
     
    #[Column(type: 'integer(11)', nullable: false)]
    private ?int $group_id =  null;
     
    #[Column(type: 'integer(11)', nullable: false)]
    private ?int $user_id = null; 
     
    #[Column(type: 'tinyInteger', nullable: false, default:1)]
    private ?int $status_id =  null;
     
    #[Column(type: 'boolean', nullable: true)]
    private ?bool $is_read_only =  false;
     
    #[Column(type: 'string(90)', nullable: true)]
    private ?string $password =  '';
     
    private DateTimeImmutable $date_created;
    
    #[Column(type: 'time', nullable: false)]
    private $time_created;
    
    private DateTimeImmutable $date_modified;
    
    #[Column(type: 'datetime', nullable: false)]
    private DateTimeImmutable $date_due;
     
    #[Column(type: 'string(100)', nullable: true)]
    private ?string $number = '';
    
    #[Column(type: 'decimal(20,2)', nullable: false, default: 0.00)]
    private ?float $discount_amount =  0.00;
     
    #[Column(type: 'decimal(20,2)', nullable: false, default: 0.00)]
    private ?float $discount_percent =  0.00;
    
    #[Column(type: 'longText', nullable: false)]
    private string $terms = '';
    
    #[Column(type: 'string(32)', nullable: false)]
    private string $url_key =  '';
    
    #[Column(type: 'integer(11)', nullable: false, default:0)]
    private ?int $payment_method =  null;
    
    #[Column(type: 'integer(11)', nullable: true)]
    private ?int $creditinvoice_parent_id =  null;
     
    public function __construct(
        int $client_id = null,
        int $user_id = null,
        int $group_id = null,
        int $status_id = 1,
        bool $is_read_only = false,
        string $password = '',
        string $number = '',
        float $discount_amount = 0.00,
        float $discount_percent = 0.00,
        string $terms = '',
        string $url_key = '',
        int $payment_method = null,
        int $creditinvoice_parent_id = null
    )
    {
        $this->client_id=$client_id;
        $this->group_id=$group_id;
        $this->user_id=$user_id;
        $this->status_id=$status_id;
        $this->is_read_only=$is_read_only;
        $this->password=$password;
        $this->date_modified=new DateTimeImmutable();        
        $this->number=$number;
        $this->discount_amount=$discount_amount;
        $this->discount_percent=$discount_percent;
        $this->terms=$terms;
        $this->url_key=$url_key;
        $this->payment_method=$payment_method;
        $this->creditinvoice_parent_id=$creditinvoice_parent_id;
    }
     
    public function setUser(User $user): void
    {
      $this->user = $user;
    }

    public function getUser(): ?User
    {
      return $this->user;
    }
    
    public function getGroup() : ?Group {
      return $this->group;
    }
    
    public function getClient() : ?Client {
      return $this->client;
    }
    
    public function getId(): ?string
    {
      return $this->id === null ? null : (string)$this->id;
    }
    
    public function setId(int $id) : void
    {
      $this->id =  $id;
    }
    
    public function getUser_id(): string
    {
     return (string)$this->user_id;
    }
    
    public function setUser_id(int $user_id) : void
    {
      $this->user_id =  $user_id;
    }
    
    public function getClient_id(): string
    {
      return (string)$this->client_id;
    }
    
    public function setClient_id(int $client_id) : void
    {
      $this->client_id =  $client_id;
    }
    
    public function getGroup_id(): string
    {
      return (string)$this->group_id;
    }
    
    public function setGroup_id(int $group_id) : void
    {
      $this->group_id = $group_id;
    }
    
    public function getStatus($status_id): string
    {
        switch ($status_id) {
            case 1:
                return 'Draft';
            case 2: 
                return 'Sent';
            case 3:
                return 'Viewed';
            case 4:
                return 'Paid';
            case 5:
                return 'Overdue';
        }
    }
    
    public function getStatus_id(): int
    {
        return $this->status_id;
    }
    
    public function setStatus_id(int $status_id) : void
    {
      $status_id === null ? $this->status_id = 1 : $this->status_id = $status_id ;
    }
    
    public function getIs_read_only(): ?bool
    {
     return $this->is_read_only;
    }
    
    public function setIs_read_only(bool $is_read_only) : void
    {
      $this->is_read_only =  $is_read_only;
    }
    
    public function getPassword(): ?string
    {
     return $this->password;
    }
    
    public function setPassword(string $password) : void
    {
      $this->password =  $password;
    }
    
    public function getDate_created(): ?DateTimeImmutable
    {
       return $this->date_created;  
    }
    
    public function setDate_created(?DateTimeImmutable $date_created) : void
    {
       $this->date_created = $date_created;
    }
    
    public function setTime_created($time_created) : void
    {
       $this->time_created = $time_created;
    }
    
    public function getTime_created(): DateTimeImmutable
    {
        return $this->time_created;
    }
    
    public function getDate_modified(): DateTimeImmutable
    {
       return $this->date_modified;
    }
    
    public function setDate_due($sR) : void
    {
        if (empty($sR->getValue('invoices_expire_after'))) { 
          $days = 30;        
        } else
        {
          $days = $sR->getValue('invoices_expire_after');          
        }
        $year = Date('Y'); $month = Date('m'); $day = Date('d');
        
        $totaldays = $day+$days;
        
        $this->date_due =  (new \DateTimeImmutable())->setDate((int)$year,(int)$month,(int)$totaldays)
                                                     ->setTime(0,0,0);
    }
    
    public function getDate_due(): ?DateTimeImmutable
    {
       return $this->date_due;  
    }
    
    public function getNumber(): ?string
    {
     return $this->number;
    }
    
    public function setNumber(string $number) : void
    {
      $this->number =  $number;
    }
    
    public function getDiscount_amount(): ?float
    {
     return $this->discount_amount;
    }
    
    public function setDiscount_amount(float $discount_amount) : void
    {
      $this->discount_amount = $discount_amount;
    }
    
    public function getDiscount_percent(): ?float
    {
     return $this->discount_percent;
    }
    
    public function setDiscount_percent(float $discount_percent) : void
    {
      $this->discount_percent = $discount_percent;
    }
    
    public function getTerms(): string
    {
     return $this->terms;
    }
    
    public function setTerms(string $terms) : void
    {
      $this->terms =  $terms;
    }
    
    public function getUrl_key(): string
    {
     return $this->url_key;
    }
    
    public function setUrl_key(string $url_key) : void
    {
      $this->url_key =  $url_key;
    }
    
    public function getPayment_method(): int
    {
     return $this->payment_method;
    }
    
    public function setPayment_method(int $payment_method) : void
    {
      $this->payment_method =  $payment_method;
    }
    
    public function getCreditinvoice_parent_id(): ?string
    {
     return (string)$this->creditinvoice_parent_id;
    }
    
    public function setCreditinvoice_parent_id($creditinvoice_parent_id) : void
    {
      $this->creditinvoice_parent_id =  $creditinvoice_parent_id;
    }
    
    public function isNewRecord(): bool
    {
        return $this->getId() === null;
    }
   
}