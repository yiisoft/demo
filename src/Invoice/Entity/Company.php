<?php

declare(strict_types=1); 

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior;
use DateTimeImmutable;  

#[Entity(repository: \App\Invoice\Company\CompanyRepository::class)]
#[Behavior\CreatedAt(field: 'date_created', column: 'date_created')]
#[Behavior\UpdatedAt(field: 'date_modified', column: 'date_modified')]   
class Company
{
    #[Column(type: 'primary')]
    private ?int $id =  null;
    
    #[Column(type: 'tinyInteger(1)', nullable: false, default:0)]
    private ?int $current =  null;
     
    #[Column(type: 'text', nullable: true)]
    private ?string $name =  '';
     
    #[Column(type: 'text', nullable: true)]
    private ?string $address_1 =  '';
     
    #[Column(type: 'text', nullable: true)]
    private ?string $address_2 =  '';
     
    #[Column(type: 'text', nullable: true)]
    private ?string $city =  '';
     
    #[Column(type: 'text', nullable: true)]
    private ?string $state =  '';
     
    #[Column(type: 'text', nullable: true)]
    private ?string $zip =  '';
     
    #[Column(type: 'text', nullable: true)]
    private ?string $country =  '';
     
    #[Column(type: 'text', nullable: true)]
    private ?string $phone =  '';
     
    #[Column(type: 'text', nullable: true)]
    private ?string $fax =  '';
     
    #[Column(type: 'text', nullable: true)]
    private ?string $email =  '';
     
    #[Column(type: 'text', nullable: true)]
    private ?string $web =  '';
     
    #[Column(type: 'datetime')]
    private DateTimeImmutable $date_created;
     
    #[Column(type: 'datetime')]
    private DateTimeImmutable $date_modified;
     
    public function __construct(
         int $id = null,
         int $current = 0,
         string $name = '',
         string $address_1 = '',
         string $address_2 = '',
         string $city = '',
         string $state = '',
         string $zip = '',
         string $country = '',
         string $phone = '',
         string $fax = '',
         string $email = '',
         string $web = '',
     )
     {
         $this->id=$id;
         $this->current=$current;
         $this->name=$name;
         $this->address_1=$address_1;
         $this->address_2=$address_2;
         $this->city=$city;
         $this->state=$state;
         $this->zip=$zip;
         $this->country=$country;
         $this->phone=$phone;
         $this->fax=$fax;
         $this->email=$email;
         $this->web=$web;
         $this->date_created = new DateTimeImmutable();
         $this->date_modified = new DateTimeImmutable();
     }
    
    public function getId(): string
    {
     return (string)$this->id;
    }
    
    public function setId(int $id) : void
    {
      $this->id =  $id;
    }
    
    public function getCurrent(): int
    {
       return $this->current;
    }
    
    public function setCurrent(int $current) : void
    {
      $this->current =  $current;
    }
    
    public function getName(): string
    {
       return $this->name;
    }
    
    public function setName(string $name) : void
    {
      $this->name =  $name;
    }
    
    public function getAddress_1(): string
    {
       return $this->address_1;
    }
    
    public function setAddress_1(string $address_1) : void
    {
      $this->address_1 =  $address_1;
    }
    
    public function getAddress_2(): string
    {
       return $this->address_2;
    }
    
    public function setAddress_2(string $address_2) : void
    {
      $this->address_2 =  $address_2;
    }
    
    public function getCity(): string
    {
       return $this->city;
    }
    
    public function setCity(string $city) : void
    {
      $this->city =  $city;
    }
    
    public function getState(): string
    {
       return $this->state;
    }
    
    public function setState(string $state) : void
    {
      $this->state =  $state;
    }
    
    public function getZip(): string
    {
       return $this->zip;
    }
    
    public function setZip(string $zip) : void
    {
      $this->zip =  $zip;
    }
    
    public function getCountry(): string
    {
       return $this->country;
    }
    
    public function setCountry(string $country) : void
    {
      $this->country =  $country;
    }
    
    public function getPhone(): string
    {
       return $this->phone;
    }
    
    public function setPhone(string $phone) : void
    {
      $this->phone =  $phone;
    }
    
    public function getFax(): string
    {
       return $this->fax;
    }
    
    public function setFax(string $fax) : void
    {
      $this->fax =  $fax;
    }
    
    public function getEmail(): string
    {
       return $this->email;
    }
    
    public function setEmail(string $email) : void
    {
      $this->email =  $email;
    }
    
    public function getWeb(): string
    {
       return $this->web;
    }
    
    public function setWeb(string $web) : void
    {
      $this->web =  $web;
    }
    
    public function getDate_created(): DateTimeImmutable
    {
        return $this->date_created;
    }

    public function getDate_modified(): DateTimeImmutable
    {
        return $this->date_modified;
    }
    
    public function isNewRecord(): bool
    {
        return $this->getId() === null;
    }
}