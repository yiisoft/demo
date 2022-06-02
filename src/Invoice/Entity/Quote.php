<?php

declare(strict_types=1); 

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use Cycle\ORM\Entity\Behavior;
use DateTimeImmutable;
use App\Invoice\Entity\Client;
use App\Invoice\Entity\Group;
use App\User\User;
use App\Invoice\Setting\SettingRepository as sR;
use App\Invoice\Quote\QuoteRepository as qR;
use App\Invoice\Group\GroupRepository as gR;
  
#[Entity(repository: \App\Invoice\Quote\QuoteRepository::class)]
#[Behavior\CreatedAt(field: 'date_created', column: 'date_created')]
#[Behavior\UpdatedAt(field: 'date_modified', column: 'date_modified')]
class Quote
{    
    #[BelongsTo(target:Client::class, nullable: true, fkAction:'NO ACTION')]
    public ?Client $client = null;    

    #[BelongsTo(target:Group::class, nullable: true, fkAction:'NO ACTION')]
    private ?Group $group = null;
        
    #[BelongsTo(target:User::class, nullable: false)]
    private ?User $user = null;
        
    #[Column(type: 'primary')]
    private ?int $id =  null;
        
    #[Column(type: 'integer(11)', nullable:true, default:0)]
    private ?int $inv_id =  null;
     
    #[Column(type: 'integer(11)', nullable:false)]
    private ?int $user_id =  null;
     
    #[Column(type: 'integer(11)', nullable:false, default:null)]
    private ?int $client_id =  null;
     
    #[Column(type: 'integer(11)', nullable:false, default:null)]
    private ?int $group_id =  null;
    
    #[Column(type: 'tinyInteger(2)', nullable:false, default:1)]
    private ?int $status_id =  null;
    
    #[Column(type: 'datetime', nullable:false)]
    private DateTimeImmutable $date_created;
     
    #[Column(type: 'datetime', nullable:false)]
    private DateTimeImmutable $date_modified;
     
    #[Column(type: 'datetime', nullable:false)]
    private DateTimeImmutable $date_expires;
     
    #[Column(type: 'string(100)', nullable:true)]
    private ?string $number =  '';
     
    #[Column(type: 'decimal(20,2)', nullable: false, default: 0.00)]
    private ?float $discount_amount =  0.00;
     
    #[Column(type: 'decimal(20,2)', nullable: false, default: 0.00)]
    private ?float $discount_percent =  0.00;
     
    #[Column(type: 'string(32)', nullable:false)]
    private string $url_key =  '';
     
    #[Column(type: 'string(90)', nullable:true)]
    private ?string $password =  '';
     
    #[Column(type: 'longText', nullable:true)]
    private ?string $notes =  '';
     
    public function __construct(
        int $inv_id = null,
        int $client_id = null,
        int $user_id = null,
        int $group_id = null,
        int $status_id = null,
        string $number = '',
        float $discount_amount = 0.00,
        float $discount_percent = 0.00,
        string $url_key = '',
        string $password = '',
        string $notes = ''
    )
    {         
        $this->inv_id=$inv_id;
        $this->client_id=$client_id;
        $this->group_id=$group_id;
        $this->user_id=$user_id;
        $this->status_id=$status_id;   
        $this->number=$number;
        $this->discount_amount=$discount_amount;
        $this->discount_percent=$discount_percent;
        $this->url_key=$url_key;
        $this->password=$password;
        $this->notes=$notes;
        $this->date_modified = new DateTimeImmutable();
    }
    
    public function getClient() : ?Client
    {
      return $this->client;
    }
    
    public function getGroup() : ?Group
    {
      return $this->group;
    }
    
    public function getUser() : ?User
    {
      return $this->user;
    }
    
    public function setUser(User $user): void
    {
        $this->user = $user;
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
    
    public function getInv_id(): ?string
    {
      return (string)$this->inv_id;        
    }
    
    public function setInv_id($inv_id) : void
    {
      $inv_id === null ? $this->inv_id = null : $this->inv_id = (int)$inv_id ;
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
      $this->group_id =  $group_id;
    }
    
    public function getStatus_id(): int
    {
        return $this->status_id;
    } 
    
    public function getStatus($status_id): string
    {
        switch ($status_id) {
            case 1:
                return 'draft';
            case 2: 
                return 'sent';
            case 3:
                return 'viewed';
            case 4:
                return 'approved';
            case 5:
                return 'rejected';
            case 6:
                return 'cancelled';
        }
    }    
    
    public function setStatus_id(int $status_id) : void
    {
      $status_id === null ? $this->status_id = 1 : $this->status_id = $status_id ;
    }
    
    public function getDate_created(): ?DateTimeImmutable
    {
       return $this->date_created;  
    }
    
    public function setDate_created(?DateTimeImmutable $date_created) : void
    {
       $this->date_created = $date_created;
    }
    
    public function getDate_modified(): DateTimeImmutable
    {
       return $this->date_modified;
    }
    
    public function setDate_expires($sR) : void
    {
        if (empty($sR->getValue('quotes_expire_after'))) { 
          $days = 30;        
        } else
        {
          $days = $sR->getValue('quotes_expire_after');          
        }
        $year = Date('Y'); $month = Date('m'); $day = Date('d');
        $totaldays = $day+$days;
        $this->date_expires =  (new \DateTimeImmutable())->setDate((int)$year,(int)$month,(int)$totaldays)->setTime(0,0,0);
    }
    
    public function getDate_expires(): ?DateTimeImmutable
    {
       return $this->date_expires;  
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
      $this->discount_percent =  $discount_percent;
    }
    
    public function getUrl_key(): string
    {
       return $this->url_key;
    }
    
    public function setUrl_key(string $url_key) : void
    {
      $this->url_key =  $url_key;
    }
    
    public function getPassword(): ?string
    {
       return $this->password;
    }
    
    public function setPassword(string $password) : void
    {
      $this->password =  $password;
    }
    
    public function getNotes(): ?string
    {
       return $this->notes;
    }
    
    public function setNotes(string $notes) : void
    {
      $this->notes =  $notes;
    }
    
    public function isNewRecord(): bool
    {
        return $this->getId() === null;
    }
}