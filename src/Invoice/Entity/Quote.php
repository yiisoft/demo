<?php

declare(strict_types=1); 

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use DateTime;
use DateTimeImmutable;
use DateInterval;
use App\Invoice\Entity\Inv;
use App\Invoice\Entity\Client;
use App\Invoice\Entity\Group;
use App\User\User;
use App\Invoice\Setting\SettingRepository;
  
 /**
 * @Entity(
 * repository="App\Invoice\Quote\QuoteRepository",
 * mapper="App\Invoice\Quote\QuoteMapper",
 * )
 */
 
 class Quote
 {
    /**
     * @BelongsTo(target="App\Invoice\Entity\Client", nullable=false, fkAction="NO ACTION")
     *
     * @var \Cycle\ORM\Promise\Reference|Client
     */
     private $client = null;    

    /**
     * @BelongsTo(target="App\Invoice\Entity\Group", nullable=false, fkAction="NO ACTION")
     *
     * @var \Cycle\ORM\Promise\Reference|Group
     */
     private $group = null;
    
    /**
     * @BelongsTo(target="App\User\User", nullable=false)
     *
     * @var \Cycle\ORM\Promise\Reference|User
     */
     private $user = null;
        
    /**
     * @Column(type="primary")
     */
     public ?int $id =  null;
     
    /**
     * @Column(type="integer(11)", nullable=false,default=0)
     */
     private ?int $inv_id =  null;
     
    /**
     * @Column(type="integer(11)", nullable=false)
     */
     private ?int $user_id =  null;
     
    /**
     * @Column(type="integer(11)", nullable=false, default=null)
     */
     private ?int $client_id =  null;
     
    /**
     * @Column(type="integer(11)", nullable=false, default=null)
     */
     private ?int $group_id =  null;
     
    /**
     * @Column(type="tinyInteger(2)", nullable=false,default=1)
     */
     private ?int $status_id =  null;
     
    /**
     * @Column(type="date", nullable=false)
     */
     private $date_created = '';
     
    /**
     * @Column(type="datetime", nullable=false)
     */
     private DateTimeImmutable $date_modified;
     
    /**
     * @Column(type="date", nullable=false)
     */
     private $date_expires = '';
     
    /**
     * @Column(type="string(100)", nullable=true)
     */
     private ?string $number =  '';
     
    /**
     * @Column(type="decimal(20,2)", nullable=true)
     */
     private ?float $discount_amount =  null;
     
    /**
     * @Column(type="decimal(20,2)", nullable=true)
     */
     private ?float $discount_percent =  null;
     
    /**
     * @Column(type="string(32)", nullable=false)
     */
     private string $url_key =  '';
     
    /**
     * @Column(type="string(90)", nullable=true)
     */
     private ?string $password =  '';
     
    /**
     * @Column(type="longText", nullable=true)
     */
     private ?string $notes =  '';
     
    /**
     * @param int $inv_id
     * @param int $client_id
     * @param int $group_id
     * @param int $status_id
     * @param string $date_created
     * @param string $date_expires
     * @param string $number
     * @param float $discount_amount
     * @param float $discount_percent
     * @param string $url_key
     * @param string $password
     * @param string $notes     
     */
     
     public function __construct(
         int $inv_id = null,
         int $client_id = null,
         int $group_id = null,
         int $status_id = null,
         $date_created = '',
         $date_expires = '',
         string $number = '',
         float $discount_amount = null,
         float $discount_percent = null,
         string $url_key = '',
         string $password = '',
         string $notes = ''
     )
     {         
         $this->inv_id=$inv_id;
         $this->client_id=$client_id;
         $this->group_id=$group_id;
         $this->status_id=$status_id;
         $this->date_expires=$date_expires;
         $this->number=$number;
         $this->discount_amount=$discount_amount;
         $this->discount_percent=$discount_percent;
         $this->url_key=$url_key;
         $this->password=$password;
         $this->notes=$notes;
         $this->date_created=new DateTimeImmutable();
         $this->date_modified= new DateTimeImmutable();
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
    
    public function getInv_id(): string
    {
      if ($this->inv_id === null) { 
          return (string)$this->inv_id = 0;          
      } else {
          return (string)$this->inv_id;          
      }          
    }
    
    public function setInv_id(int $inv_id) : void
    {
      $inv_id === null ? $this->inv_id = 0 : $this->inv_id = $inv_id ;
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
        switch ($this->status_id) {
            case 1:
                return 'Draft';
                break;
            case 2: 
                return 'Sent';
                break;
            case 3:
                return 'Viewed';
                break;
            case 4:
                return 'Approved';
                break;
            case 5:
                return 'Rejected';
                break;
        }
    }
    
    public function setStatus_id(int $status_id) : void
    {
      $status_id === null ? $this->status_id = 1 : $this->status_id = $status_id ;
    }
    
    public function getDate_created(): DateTimeImmutable
    {
      return $this->date_created;
    }
    
    public function setDate_created(DateTime $date_created) : void
    {
      $this->date_created =  $date_created;
    }
    
    public function getDate_modified(): DateTimeImmutable
    {
       return $this->date_modified;
    }
    
    public function getDate_expires(): DateTimeImmutable
    {
      if (isset($this->date_expires) && !empty($this->date_expires)){
       return $this->date_expires;
     };
    }
    
    public function setDate_expires(DateTime $date_expires, SettingRepository $s) : void
    {
        if (empty($s->getValue('quotes_expire_after'))) { 
          $days = 30;        
        } else
        {
          $days = $s->getValue('quotes_expire_after');          
        }
        $date_expires->add(new DateInterval('P' . $days . 'D'));
        $this->date_expires =  $date_expires->format('Y-m-d');
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
      $this->discount_amount =  $discount_amount;
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