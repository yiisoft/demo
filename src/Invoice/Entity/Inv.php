<?php

declare(strict_types=1); 

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use App\User\User;
use App\Invoice\Entity\Group;
use App\Invoice\Entity\Client;
use App\Invoice\Setting\SettingRepository;
use DateTime;
use DateTimeImmutable;
use DateInterval;

/**
 * @Entity(
 * repository="App\Invoice\Inv\InvRepository"
 * )
 */
 
 class Inv
 {
    /**
     * @BelongsTo(target="App\User\User", nullable=false)
     *
     * @var \Cycle\ORM\Promise\Reference|User
     */
    private $user = null;
    
    /**
     * @BelongsTo(target="Group", nullable=false, fkAction="NO ACTION")
     *
     * @var \Cycle\ORM\Promise\Reference|Group
     */
     private $group = null;
    

    /**
     * @BelongsTo(target="Client", nullable=false, fkAction="NO ACTION")
     *
     * @var \Cycle\ORM\Promise\Reference|Client
     */
     private $client = null;
     
     /**
     * @Column(type="primary", nullable=false)
     */
     public ?int $id =  null;
     
    /**
     * @Column(type="integer(11)", nullable=false)
     */
     public ?int $client_id =  null;
     
    /**
     * @Column(type="integer(11)", nullable=false)
     */
     public ?int $group_id =  null;
     
     /**
     * @Column(type="integer(11)", nullable=false)
     */
     public ?int $user_id = null; 
     
    /**
     * @Column(type="tinyInteger", nullable=false,default=1)
     */
     private ?int $status_id =  null;
     
    /**
     * @Column(type="boolean", nullable=true)
     */
     private ?bool $is_read_only =  false;
     
    /**
     * @Column(type="string", nullable=true)
     */
     private ?string $password =  '';
     
    /**
     * @Column(type="date", nullable=false)
     */
     private $date_created =  '';
     
    /**
     * @Column(type="time", nullable=false,default="00:00:00")
     */
     private DateTimeImmutable $time_created;
     
    /**
     * @Column(type="datetime", nullable=false)
     */
     private DateTimeImmutable $date_modified;
     
    /**
     * @Column(type="date", nullable=false)
     */
     private $date_due = '';
     
    /**
     * @Column(type="string", nullable=true)
     */
     public ?string $number = '';
     
    /**
     * @Column(type="decimal(20,2)", nullable=true)
     */
     private ?float $discount_amount =  null;
     
    /**
     * @Column(type="decimal(20,2)", nullable=true)
     */
     private ?float $discount_percent =  null;
     
    /**
     * @Column(type="longText", nullable=false)
     */
     private string $terms = '';
     
    /**
     * @Column(type="string(32)", nullable=false)
     */
     private string $url_key =  '';
     
    /**
     * @Column(type="integer(11)", nullable=false,default=0)
     */
     private ?int $payment_method =  null;
     
    /**
     * @Column(type="integer(11)", nullable=true)
     */
     private ?int $creditinvoice_parent_id =  null;
     
     public function __construct(
         int $client_id = null,
         int $group_id = null,
         int $status_id = null,
         bool $is_read_only = false,
         string $password = '',
         string $date_created = '',
         $date_due = '',
         string $number = '',
         float $discount_amount = null,
         float $discount_percent = null,
         string $terms = '',
         string $url_key = '',
         int $payment_method = null,
         int $creditinvoice_parent_id = null
     )
     {
         $this->client_id=$client_id;
         $this->group_id=$group_id;
         $this->status_id=$status_id;
         $this->is_read_only=$is_read_only;
         $this->password=$password;
         $this->date_created=$date_created;
         $this->time_created=new DateTimeImmutable();
         $this->date_modified=new DateTimeImmutable();
         $this->date_due=$date_due;
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
      $this->group_id =  $group_id;
    }
    
    public function getStatus_id(): string
    {
     return (string)$this->status_id;
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
    
    public function getDate_created() : ?DateTimeImmutable  
    {
        if (isset($this->date_created) && !empty($this->date_created)){
            return $this->date_created;            
        }
    }    
    
    public function setDate_created(?DateTime $date_created): void
    {
        $this->date_created = $date_created->format('Y-m-d');
    }
    
    public function getTime_created(): DateTimeImmutable
    {
        return $this->time_created;
    }
    
    public function setTime_created(DateTime $time_created) : void
    {
        if (empty($this->time_created)){
            $this->time_created =  new DateTime(date('H:i:s'));
        } 
    }
    
    public function getDate_modified(): DateTimeImmutable
    {
        return $this->date_modified;
    }
        
    public function getDate_due(): ?DateTimeImmutable  
    {
        if (isset($this->date_due) && !empty($this->date_due)){
            return $this->date_due;            
        }        
    } 
    
    public function setDate_due(DateTime $date_due, SettingRepository $s) : void
    {
        $date_due->add(new DateInterval('P' . $s->getValue('invoices_due_after') . 'D'));
        $this->date_due = $date_due->format('Y-m-d');
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
      $discount_amount === null ? $this->discount_amount = 0.00 : $this->discount_amount =  $discount_amount;
    }
    
    public function getDiscount_percent(): ?float
    {
     return $this->discount_percent;
    }
    
    public function setDiscount_percent(float $discount_percent) : void
    {
      $discount_percent === null ? $this->discount_percent =  0.00 : $this->discount_percent =  $discount_percent;
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
    
    public function setCreditinvoice_parent_id(int $creditinvoice_parent_id) : void
    {
      $creditinvoice_parent_id === null ? $this->creditinvoice_parent_id = null : $this->creditinvoice_parent_id =  $creditinvoice_parent_id;
    }
    
    public function isNewRecord(): bool
    {
        return $this->getId() === null;
    }
    
   
}