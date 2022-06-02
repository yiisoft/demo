<?php

declare(strict_types=1); 

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use DateTimeImmutable;
use App\Invoice\Entity\Company;

 #[Entity(repository: \App\Invoice\CompanyPrivate\CompanyPrivateRepository::class)]
 #[Behavior\UpdatedAt(field: 'date_modified', column: 'date_modified')]       
 class CompanyPrivate
 {       
   
    #[BelongsTo(target:Company::class, nullable: false)]
    private ?Company $company = null;
    
    #[Column(type: 'primary')]
    private ?int $id =  null;
     
    #[Column(type: 'integer(11)', nullable: false)]
    private ?int $company_id =  null;
     
    #[Column(type: 'text', nullable: true)]
    private ?string $vat_id =  '';
     
    #[Column(type: 'text', nullable: true)]
    private ?string $tax_code =  '';
     
    #[Column(type: 'string(34)', nullable: true)]
    private ?string $iban =  '';
     
    #[Column(type: 'bigInteger(20)', nullable: true)]
    private ?int $gln =  null;
     
    #[Column(type: 'string(7)', nullable: true)]
    private ?string $rcc =  '';
     
    #[Column(type: 'datetime')]
    private DateTimeImmutable $date_created;
     
    #[Column(type: 'datetime')]
    private DateTimeImmutable $date_modified;
     
    public function __construct(
         int $id = null,
         int $company_id = null,
         string $vat_id = '',
         string $tax_code = '',
         string $iban = '',
         int $gln = null,
         string $rcc = '',
     )
     {
         $this->id=$id;
         $this->company_id=$company_id;
         $this->vat_id=$vat_id;
         $this->tax_code=$tax_code;
         $this->iban=$iban;
         $this->gln=$gln;
         $this->rcc=$rcc;
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
    
    public function getVat_id(): string
    {
     return (string)$this->vat_id;
    }
    
    public function setVat_id(string $vat_id) : void
    {
      $this->vat_id =  $vat_id;
    }
    
    public function getTax_code(): string
    {
       return $this->tax_code;
    }
    
    public function setTax_code(string $tax_code) : void
    {
      $this->tax_code =  $tax_code;
    }
    
    public function getIban(): string
    {
       return $this->iban;
    }
    
    public function setIban(string $iban) : void
    {
      $this->iban =  $iban;
    }
    
    public function getGln(): int
    {
       return $this->gln;
    }
    
    public function setGln(int $gln) : void
    {
      $this->gln =  $gln;
    }
    
    public function getRcc(): string
    {
       return $this->rcc;
    }
    
    public function setRcc(string $rcc) : void
    {
      $this->rcc =  $rcc;
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