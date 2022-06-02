<?php
declare(strict_types=1); 

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior;
use DateTime;
use DateTimeImmutable; 

#[Entity(repository:\App\Invoice\Client\ClientRepository::class)]
#[Behavior\CreatedAt(field: 'client_date_created', column: 'client_date_created')]
#[Behavior\UpdatedAt(field: 'client_date_modified', column: 'client_date_modified')]    
class Client
 {
    #[Column(type: 'primary')]
    public ?int $id = null;
     
    #[Column(type: 'datetime')]
    private DateTimeImmutable $client_date_created;
    
    #[Column(type: 'datetime')]    
    private DateTimeImmutable $client_date_modified;
     
    #[Column(type: 'text')]
    private string $client_name = '';
     
    #[Column(type: 'text', nullable: true)]
    private ?string $client_address_1 =  '';
     
    #[Column(type: 'text', nullable: true)]
    private ?string $client_address_2 =  '';
     
    #[Column(type: 'text', nullable: true)]
    private ?string $client_city =  '';
     
    #[Column(type: 'text', nullable: true)]
    private ?string $client_state =  '';
     
    #[Column(type: 'text', nullable: true)]
    private ?string $client_zip =  '';
     
    #[Column(type: 'text', nullable: true)]
    private ?string $client_country =  '';
     
    #[Column(type: 'text', nullable: true)]
    private ?string $client_phone =  '';
     
    #[Column(type: 'text', nullable: true)]
    private ?string $client_fax =  '';
     
    #[Column(type: 'text', nullable: true)]
    private ?string $client_mobile =  '';
     
    #[Column(type: 'text', nullable: true)]
    private string $client_email =  '';
     
    #[Column(type: 'text', nullable: true)]
    private ?string $client_web =  '';
     
    #[Column(type: 'text', nullable: true)]
    private ?string $client_vat_id =  '';
     
    #[Column(type: 'text', nullable: true)]
    private ?string $client_tax_code =  '';
     
    #[Column(type: 'string(151)', nullable: true)]
    private ?string $client_language =  '';
     
    #[Column(type: 'bool', default: false)]
    private bool $client_active = false;
     
    #[Column(type: 'string(151)', nullable: true)]
    private ?string $client_surname = '';
     
    #[Column(type: 'string(16)', nullable: true)]
    private ?string $client_avs =  '';
     
    #[Column(type: 'string(151)', nullable: true)]
    private ?string $client_insurednumber =  '';
     
    #[Column(type: 'string(30)', nullable: true)]
    private ?string $client_veka =  '';  
    
    #[Column(type:'date', nullable: true)]
    private $client_birthdate;
    
    #[Column(type: 'tinyInteger(4)', nullable: false, default: 0)]
    private ?int $client_gender = null;
     
    public function __construct(string $client_name = '', string $client_address_1='',string $client_address_2='',string $client_city='',
            string $client_state='',string $client_zip='',string $client_country='',string $client_phone='',string $client_fax='',
            string $client_mobile='', string $client_email ='', string $client_web='', string $client_vat_id='', string $client_tax_code='',
            string $client_language='', bool $client_active=false, string $client_surname='', string $client_avs='', string $client_insurednumber='',
            string $client_veka='', $client_birthdate = '', int $client_gender=0
    )
    {
        $this->client_name = $client_name;  
        $this->client_address_1 = $client_address_1;
        $this->client_address_2 = $client_address_2;
        $this->client_city = $client_city;
        $this->client_state = $client_state;
        $this->client_zip = $client_zip;
        $this->client_country = $client_country;
        $this->client_phone = $client_phone;
        $this->client_fax = $client_fax;
        $this->client_mobile = $client_mobile;
        $this->client_email = $client_email;
        $this->client_web = $client_web;
        $this->client_vat_id = $client_vat_id;
        $this->client_tax_code = $client_tax_code;
        $this->client_language = $client_language;
        $this->client_active = $client_active;
        $this->client_surname = $client_surname;
        $this->client_avs = $client_avs;
        $this->client_insurednumber = $client_insurednumber;
        $this->client_veka = $client_veka;
        $this->client_birthdate = $client_birthdate;
        $this->client_gender = $client_gender;
        $this->client_date_created = new DateTimeImmutable();
        $this->client_date_modified = new DateTimeImmutable();
    }
    
    public function getClient_id(): ?int
    {
        return $this->id;
    }
    
    public function getClient_date_created(): DateTimeImmutable
    {
        return $this->client_date_created;
    }

    public function getClient_date_modified(): DateTimeImmutable
    {
        return $this->client_date_modified;
    }
    
    public function getClient_name(): string
    {
       return $this->client_name;
    }
    
    public function setClient_name(string $client_name) : void
    {
       $this->client_name =  $client_name;
    }
    
    public function getClient_address_1(): ?string
    {
       return $this->client_address_1;
    }
    
    public function setClient_address_1(string $client_address_1) : void
    {
      $this->client_address_1 =  $client_address_1;
    }
    
    public function getClient_address_2(): ?string
    {
       return $this->client_address_2;
    }
    
    public function setClient_address_2(string $client_address_2) : void
    {
      $this->client_address_2 =  $client_address_2;
    }
    
    public function getClient_city(): ?string
    {
       return $this->client_city;
    }
    
    public function setClient_city(string $client_city) : void
    {
      $this->client_city =  $client_city;
    }
    
    public function getClient_state(): ?string
    {
       return $this->client_state;
    }
    
    public function setClient_state(string $client_state) : void
    {
      $this->client_state =  $client_state;
    }
    
    public function getClient_zip(): ?string
    {
       return $this->client_zip;
    }
    
    public function setClient_zip(string $client_zip) : void
    {
      $this->client_zip =  $client_zip;
    }
    
    public function getClient_country(): ?string
    {
       return $this->client_country;
    }
    
    public function setClient_country(string $client_country) : void
    {
      $this->client_country =  $client_country;
    }
    
    public function getClient_phone(): ?string
    {
       return $this->client_phone;
    }
    
    public function setClient_phone(string $client_phone) : void
    {
      $this->client_phone =  $client_phone;
    }
    
    public function getClient_fax(): ?string
    {
       return $this->client_fax;
    }
    
    public function setClient_fax(string $client_fax) : void
    {
      $this->client_fax =  $client_fax;
    }
    
    public function getClient_mobile(): ?string
    {
       return $this->client_mobile;
    }
    
    public function setClient_mobile(string $client_mobile) : void
    {
      $this->client_mobile =  $client_mobile;
    }
    
    public function getClient_email(): string
    {
       return $this->client_email;
    }
    
    public function setClient_email(string $client_email) : void
    {
      $this->client_email =  $client_email;
    }
    
    public function getClient_web(): ?string
    {
       return $this->client_web;
    }
    
    public function setClient_web(string $client_web) : void
    {
      $this->client_web =  $client_web;
    }
    
    public function getClient_vat_id(): ?string
    {
     return (string)$this->client_vat_id;
    }
    
    public function setClient_vat_id(string $client_vat_id) : void
    {
      $this->client_vat_id =  $client_vat_id;
    }
    
    public function getClient_tax_code(): ?string
    {
       return $this->client_tax_code;
    }
    
    public function setClient_tax_code(string $client_tax_code) : void
    {
      $this->client_tax_code =  $client_tax_code;
    }
    
    public function getClient_language(): ?string
    {
       return $this->client_language;
    }
    
    public function setClient_language(string $client_language) : void
    {
       $this->client_language =  $client_language;
    }
    
    public function getClient_active(): bool
    {
       return $this->client_active;
    }
    
    public function setClient_active(bool $client_active) : void
    {
      $this->client_active =  $client_active;
    }
    
    public function getClient_surname(): ?string
    {
       return $this->client_surname;
    }
    
    public function setClient_surname(string $client_surname) : void
    {
      $this->client_surname =  $client_surname;
    }
    
    public function getClient_avs(): ?string
    {
       return $this->client_avs;
    }
    
    public function setClient_avs(string $client_avs) : void
    {
      $this->client_avs =  $client_avs;
    }
    
    public function getClient_insurednumber(): ?string
    {
       return $this->client_insurednumber;
    }
    
    public function setClient_insurednumber(string $client_insurednumber) : void
    {
      $this->client_insurednumber =  $client_insurednumber;
    }
    
    public function getClient_veka(): ?string
    {
       return $this->client_veka;
    }
    
    public function setClient_veka(string $client_veka) : void
    {
      $this->client_veka =  $client_veka;
    } 
    
    //cycle 
    public function getClient_birthdate() : ?DateTimeImmutable  
    {
        if (isset($this->client_birthdate) && !empty($this->client_birthdate)){
            return $this->client_birthdate;            
        }
        if (empty($this->client_birthdate)){
            return $this->client_birthdate = null;
        }
    }    
    
    public function setClient_birthdate(?DateTime $client_birthdate): void
    {
        $this->client_birthdate = $client_birthdate;
    }
    
    public function getClient_gender(): int
    {
       return $this->client_gender;
    }
    
    public function setClient_gender(int $client_gender) : void
    {
      $this->client_gender =  $client_gender;
    }
    
    public function isNewRecord(): bool
    {
        return $this->getClient_id() === null;
    }
}