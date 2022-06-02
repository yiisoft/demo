<?php

declare(strict_types=1); 

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use Cycle\ORM\Entity\Behavior;
use \DateTimeImmutable;
use App\Invoice\Entity\Company;

#[Entity(repository: \App\Invoice\Profile\ProfileRepository::class)] 
#[Behavior\UpdatedAt(field: 'date_modified', column: 'date_modified')]
class Profile
{
    #[BelongsTo(target:Company::class, nullable: false)]
    private ?Company $company = null;
        
    #[Column(type: 'primary')]
    private ?int $id =  null;
     
    #[Column(type: 'integer(11)',nullable: false)] 
    private ?int $company_id =  null;
    
    #[Column(type: 'tinyInteger(11)', default:0)] 
    private ?int $current =  0;
   
    #[Column(type: 'text',nullable: true)] 
    private ?string $mobile =  '';
     
    #[Column(type: 'text',nullable: true)] 
    private ?string $email =  '';
     
    #[Column(type: 'text',nullable: true)] 
    private ?string $description =  '';
     
    #[Column(type: 'datetime')] 
    private DateTimeImmutable $date_created;
     
    #[Column(type: 'datetime')] 
    private DateTimeImmutable $date_modified;
     
    public function __construct(
        int $id = null,
        int $company_id = null,
        int $current = 0,
        string $mobile = '',
        string $email = '',
        string $description = '',
    )
    {
        $this->id=$id;
        $this->company_id=$company_id;
        $this->current=$current;
        $this->mobile=$mobile;
        $this->email=$email;
        $this->description=$description;
        $this->date_created= new DateTimeImmutable();
        $this->date_modified= new DateTimeImmutable();
    }
    
    public function getCompany() : ?Company
    {
      return $this->company;
    }
    
    public function getId(): string
    {
     return (string)$this->id;
    }
    
    public function setId(int $id) : void
    {
      $this->id =  $id;
    }
    
    public function getCompany_id(): string
    {
     return (string)$this->company_id;
    }
    
    public function setCompany_id(int $company_id) : void
    {
      $this->company_id =  $company_id;
    }
    
    public function getCurrent(): int
    {
       return $this->current;
    }
    
    public function setCurrent(int $current) : void
    {
      $this->current =  $current;
    }
    
    public function getMobile(): string
    {
       return $this->mobile;
    }
    
    public function setMobile(string $mobile) : void
    {
      $this->mobile =  $mobile;
    }
    
    public function getEmail(): string
    {
       return $this->email;
    }
    
    public function setEmail(string $email) : void
    {
      $this->email =  $email;
    }
    
    public function getDescription(): string
    {
       return $this->description;
    }
    
    public function setDescription(string $description) : void
    {
      $this->description =  $description;
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