<?php
declare(strict_types=1); 

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use DateTimeImmutable;
use App\User\User;
use App\Invoice\Setting\SettingRepository;

#[Entity(repository:\App\Invoice\UserInv\UserInvRepository::class)]
#[Behavior\CreatedAt(field: 'date_created', column: 'date_created')]
#[Behavior\UpdatedAt(field: 'date_modified', column: 'date_modified')]    
class UserInv
 {
    #[Column(type:'primary')]
    private ?int $id = null;
    
    #[BelongsTo(target:User::class, nullable: false)]
    private ?User $user = null;
    
    #[Column(type: 'integer(11)', nullable:false)]
    private ?int $user_id =  null;
    
    #[Column(type:"integer(11)", nullable:false, default:0)]
    private ?int $type = null;
    
    #[Column(type:'bool', typecast:'bool', default:false)]
    private ?bool $active = false;
     
    #[Column(type: 'datetime', nullable:false)]
    private DateTimeImmutable $date_created;
     
    #[Column(type: 'datetime', nullable:false)]
    private DateTimeImmutable $date_modified;
    
    #[Column(type:"string(191)", nullable:true, default:'system')]
    private ?string $language =  '';
    
    #[Column(type:"text", nullable:true)]
    private ?string $name =  '';
     
    #[Column(type:"text", nullable:true)]
    private ?string $company =  '';
     
    #[Column(type:"text", nullable:true)]
    private ?string $address_1 =  '';
     
    #[Column(type:"text", nullable:true)]
    private ?string $address_2 =  '';
     
    #[Column(type:"text", nullable:true)]
    private ?string $city =  '';
     
    #[Column(type:"text", nullable:true)]
    private ?string $state =  '';
     
    #[Column(type:"text", nullable:true)]
    private ?string $zip =  '';
     
    #[Column(type:"text", nullable:true)]
    private ?string $country =  '';
     
    #[Column(type:"text", nullable:true)]
    private ?string $phone =  '';
     
    #[Column(type:"text", nullable:true)]
    private ?string $fax =  '';
     
    #[Column(type:"text", nullable:true)]
    private ?string $mobile =  '';
     
    #[Column(type:"text", nullable:true)]
    private ?string $email =  '';
     
    #[Column(type:"string(60)",nullable:false)]
    private string $password =  '';
     
    #[Column(type:"text", nullable:true)]
    private ?string $web =  '';
     
    #[Column(type:"text", nullable:true)]
    private ?string $vat_id =  '';
     
    #[Column(type:"text", nullable:true)]
    private ?string $tax_code =  '';
        
    #[Column(type:'bool', typecast:'bool', default:false)]
    private ?bool $all_clients = false;
    
    #[Column(type:"string(100)",nullable:true)]
    private ?string $salt =  '';
     
    #[Column(type:"string(100)",nullable:true)]
    private ?string $passwordreset_token =  '';
     
    #[Column(type:"string(40)",nullable:true)]
    private ?string $subscribernumber =  '';
     
    #[Column(type:"string(34))",nullable:true)]
    private ?string $iban =  '';
    
    #[Column(type:"bigInteger(20)",nullable:true)]
    private ?int $gln =  null;
     
    #[Column(type:"string(7)",nullable:true)]
    private ?string $rcc =  '';
     
    public function __construct(
        int $id = null,
        int $user_id = null,
        int $type = null,
        bool $active = false,
        string $language = '',
        string $name = '',
        string $company = '',
        string $address_1 = '',
        string $address_2 = '',
        string $city = '',
        string $state = '',
        string $zip = '',
        string $country = '',
        string $phone = '',
        string $fax = '',
        string $mobile = '',
        string $email = '',
        string $password = '',
        string $web = '',
        string $vat_id = '',
        string $tax_code = '',
        bool $all_clients = false,
        string $salt = '',
        string $passwordreset_token = '',
        string $subscribernumber = '',
        string $iban = '',
        int $gln = null,
        string $rcc = '',
     )
     {
        $this->id=$id;
        $this->user_id=$user_id;
        $this->type=$type;
        $this->active=$active;        
        $this->date_created=new DateTimeImmutable();
        $this->date_modified=new DateTimeImmutable();
        $this->language=$language;
        $this->name=$name;
        $this->company=$company;
        $this->address_1=$address_1;
        $this->address_2=$address_2;
        $this->city=$city;
        $this->state=$state;
        $this->zip=$zip;
        $this->country=$country;
        $this->phone=$phone;
        $this->fax=$fax;
        $this->mobile=$mobile;
        $this->email=$email;
        $this->password=$password;
        $this->web=$web;
        $this->vat_id=$vat_id;
        $this->tax_code=$tax_code;
        $this->all_clients=$all_clients;
        $this->salt=$salt;
        $this->passwordreset_token=$passwordreset_token;
        $this->subscribernumber=$subscribernumber;
        $this->iban=$iban;
        $this->gln=$gln;
        $this->rcc=$rcc;
     }
    
    public function getId(): string
    {
     return (string)$this->id;
    }
    
    public function setId(int $id) : void
    {
       $this->id = $id;
    }
    
    public function setUser(User $user): void
    {
        $this->user = $user;
    }
    
    public function getUser() : ?User
    {
      return $this->user;
    }
    
    public function getUser_id(): string
    {
     return (string)$this->user_id;
    }
    
    public function setUser_id(int $user_id) : void
    {
      $this->user_id =  $user_id;
    }
    
    public function getType(): int
    {
       return $this->type;
    }
    
    public function setType(int $type) : void
    {
       $this->type =  $type;
    }
    
    public function getActive(): bool
    {
       return $this->active;
    }
    
    public function getActiveLabel(SettingRepository $sR): string
    {
        return $this->active ? '<span class="label active">'.$sR->trans('yes').'</span>' : '<span class="label inactive">'.$sR->trans('no').'</span>';
    }
    
    public function setActive(bool $active) : void
    {
       $this->active =  $active;
    }
    
    public function getDate_created(): DateTimeImmutable
    {
       return $this->date_created;
    }
    
    public function getDate_modified(): DateTimeImmutable
    {
       return $this->date_modified;
    }
    
    public function getLanguage(): ?string
    {
       return $this->language;
    }
    
    public function setLanguage(string $language) : void
    {
       $this->language =  $language;
    }
    
    public function getName(): ?string
    {
       return $this->name;
    }
    
    public function setName(string $name) : void
    {
       $this->name =  $name;
    }
    
    public function getCompany(): ?string
    {
       return $this->company;
    }
    
    public function setCompany(string $company) : void
    {
      $this->company =  $company;
    }
    
    public function getAddress_1(): ?string
    {
       return $this->address_1;
    }
    
    public function setAddress_1(string $address_1) : void
    {
       $this->address_1 =  $address_1;
    }
    
    public function getAddress_2(): ?string
    {
       return $this->address_2;
    }
    
    public function setAddress_2(string $address_2) : void
    {
       $this->address_2 =  $address_2;
    }
    
    public function getCity(): ?string
    {
       return $this->city;
    }
    
    public function setCity(string $city) : void
    {
       $this->city =  $city;
    }
    
    public function getState(): ?string
    {
       return $this->state;
    }
    
    public function setState(string $state) : void
    {
       $this->state =  $state;
    }
    
    public function getZip(): ?string
    {
       return $this->zip;
    }
    
    public function setZip(string $zip) : void
    {
       $this->zip =  $zip;
    }
    
    public function getCountry(): ?string
    {
       return $this->country;
    }
    
    public function setCountry(string $country) : void
    {
      $this->country =  $country;
    }
    
    public function getPhone(): ?string
    {
       return $this->phone;
    }
    
    public function setPhone(string $phone) : void
    {
       $this->phone =  $phone;
    }
    
    public function getFax(): ?string
    {
       return $this->fax;
    }
    
    public function setFax(string $fax) : void
    {
      $this->fax =  $fax;
    }
    
    public function getMobile(): ?string
    {
       return $this->mobile;
    }
    
    public function setMobile(string $mobile) : void
    {
       $this->mobile =  $mobile;
    }
    
    public function getEmail(): ?string
    {
       return $this->email;
    }
    
    public function setEmail(string $email) : void
    {
       $this->email =  $email;
    }
    
    public function getPassword(): string
    {
       return $this->password;
    }
    
    public function setPassword(string $password) : void
    {
       $this->password =  $password;
    }
    
    public function getWeb(): ?string
    {
       return $this->web;
    }
    
    public function setWeb(string $web) : void
    {
      $this->web =  $web;
    }
    
    public function getVat_id(): ?string
    {
     return (string)$this->vat_id;
    }
    
    public function setVat_id(string $vat_id) : void
    {
      $this->vat_id =  $vat_id;
    }
    
    public function getTax_code(): ?string
    {
       return $this->tax_code;
    }
    
    public function setTax_code(string $tax_code) : void
    {
      $this->tax_code =  $tax_code;
    }
    
    public function getAll_clients(): bool
    {
       return $this->all_clients;
    }
    
    public function setAll_clients(bool $all_clients) : void
    {
      $this->all_clients =  $all_clients;
    }
    
    public function getSalt(): ?string
    {
       return $this->salt;
    }
    
    public function setSalt(string $salt) : void
    {
      $this->salt =  $salt;
    }
    
    public function getPasswordreset_token(): ?string
    {
       return $this->passwordreset_token;
    }
    
    public function setPasswordreset_token(string $passwordreset_token) : void
    {
      $this->passwordreset_token =  $passwordreset_token;
    }
    
    public function getSubscribernumber(): ?string
    {
       return $this->subscribernumber;
    }
    
    public function setSubscribernumber(string $subscribernumber) : void
    {
      $this->subscribernumber =  $subscribernumber;
    }
    
    public function getIban(): ?string
    {
       return $this->iban;
    }
    
    public function setIban(string $iban) : void
    {
      $this->iban =  $iban;
    }
    
    public function getGln(): ?int
    {
       return $this->gln;
    }
    
    public function setGln(int $gln) : void
    {
      $this->gln =  $gln;
    }
    
    public function getRcc(): ?string
    {
       return $this->rcc;
    }
    
    public function setRcc(string $rcc) : void
    {
      $this->rcc =  $rcc;
    }
    
    public function isNewRecord(): bool
    {
        return $this->getId() === null;
    }
}