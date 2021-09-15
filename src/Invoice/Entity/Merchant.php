<?php

declare(strict_types=1); 

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use App\Invoice\Entity\Inv;
use DateTime;
use DateTimeImmutable;

  
 /**
* @Entity(
 * repository="App\Invoice\Merchant\MerchantRepository",
 * )
 */
 
 class Merchant
 {
       
   
    /**
     * @BelongsTo(target="Inv", nullable=false, fkAction="NO ACTION")
     *
     * @var \Cycle\ORM\Promise\Reference|Inv
     */
     private $inv = null;
    
    
        /**
     * @Column(type="primary")
     */
     public ?int $id =  null;
     
    /**
     * @Column(type="integer(11)", nullable=false)
     */
     private ?int $inv_id =  null;
     
    /**
     * @Column(type="boolean",default=1,nullable=true)
     */
     private ?bool $successful = true;
     
    /**
     * @Column(type="date", nullable=false)
     */
     private $date =  '';
     
    /**
     * @Column(type="string(35)", nullable=false)
     */
     private string $driver =  '';
     
    /**
     * @Column(type="string(255)", nullable=false)
     */
     private string $response =  '';
     
    /**
     * @Column(type="string(255)", nullable=false)
     */
     private string $reference =  '';
     
     public function __construct(
         int $id = null,
         int $inv_id = null,
         bool $successful = true,
         string $date = '',
         string $driver = '',
         string $response = '',
         string $reference = ''
     )
     {
         $this->id=$id;
         $this->inv_id=$inv_id;
         $this->successful=$successful;
         $this->date=$date;
         $this->driver=$driver;
         $this->response=$response;
         $this->reference=$reference;
     }
    
    public function getInv() : ?Inv
 {
      return $this->inv;
    }
    
    public function getId(): string
    {
     return (string)$this->id;
    }
    
    public function setId(int $id) : void
    {
      $this->id =  $id;
    }
    
    public function getInv_id(): string
    {
     return (string)$this->inv_id;
    }
    
    public function setInv_id(int $inv_id) : void
    {
      $this->inv_id =  $inv_id;
    }
    
    public function getSuccessful(): bool
    {
       return $this->successful;
    }
    
    public function setSuccessful(bool $successful) : void
    {
      $this->successful =  $successful;
    }
    
    public function getDate(): DateTimeImmutable
    {
      if (isset($this->date) && !empty($this->date)){
       return $this->date;
     };
    }
    
    public function setDate(DateTime $date) : void
    {
      $this->date =  $date->format('Y-m-d');
    }
    
    public function getDriver(): string
    {
       return $this->driver;
    }
    
    public function setDriver(string $driver) : void
    {
      $this->driver =  $driver;
    }
    
    public function getResponse(): string
    {
       return $this->response;
    }
    
    public function setResponse(string $response) : void
    {
      $this->response =  $response;
    }
    
    public function getReference(): string
    {
       return $this->reference;
    }
    
    public function setReference(string $reference) : void
    {
      $this->reference =  $reference;
    }
}