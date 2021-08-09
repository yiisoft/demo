<?php

declare(strict_types=1); 

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use App\Invoice\Entity\Client;
  
 /**
* @Entity(
 * repository="App\Invoice\ClientCustom\ClientCustomRepository",
 * )
 */
 
 class ClientCustom
 {
       
   
    /**
     * @BelongsTo(target="Client", nullable=false)
     *
     * @var \Cycle\ORM\Promise\Reference|Client
     */
     private $client = null;
    
    
        /**
     * @Column(type="primary")
     */
     public ?int $id =  null;
     
    /**
     * @Column(type="integer(11)", nullable=false)
     */
     private ?int $client_id =  null;
     
    /**
     * @Column(type="integer(11)", nullable=false)
     */
     private ?int $fieldid =  null;
     
    /**
     * @Column(type="text", nullable=true)
     */
     private ?string $fieldvalue =  '';
     
     public function __construct(
          int $id = null,
         int $client_id = null,
         int $fieldid = null,
         string $fieldvalue = ''
     )
     {
         $this->id=$id;
         $this->client_id=$client_id;
         $this->fieldid=$fieldid;
         $this->fieldvalue=$fieldvalue;
     }
    
    public function getClient() : ?Client
 {
      return $this->client;
    }
    
    public function getId(): string
    {
     return (string)$this->id;
    }
    
    public function setId(int $id) : void
    {
      $this->id =  $id;
    }
    
    public function getClient_id(): string
    {
     return (string)$this->client_id;
    }
    
    public function setClient_id(int $client_id) : void
    {
      $this->client_id =  $client_id;
    }
    
    public function getFieldid(): string
    {
     return (string)$this->fieldid;
    }
    
    public function setFieldid(int $fieldid) : void
    {
      $this->fieldid =  $fieldid;
    }
    
    public function getFieldvalue(): ?string
    {
       return $this->fieldvalue;
    }
    
    public function setFieldvalue(string $fieldvalue) : void
    {
      $this->fieldvalue =  $fieldvalue;
    }
}