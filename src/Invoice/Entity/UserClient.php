<?php
declare(strict_types=1); 

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use App\User\User;
use App\Invoice\Entity\Client;

#[Entity(repository: \App\Invoice\UserClient\UserClientRepository::class)]
class UserClient
{    
    #[BelongsTo(target:\App\User\User::class, nullable: false)]
    private ?User $user = null;
    
    #[BelongsTo(target:\App\Invoice\Entity\Client::class, nullable: false)]
    private ?Client $client = null;
        
    #[Column(type: 'primary')]
    private ?int $id =  null;
    
    #[Column(type: 'integer(11)', nullable:false)]
    private ?int $user_id =  null;
     
    #[Column(type: 'integer(11)', nullable:false)]
    private ?int $client_id =  null;
     
    public function __construct(
        int $id = null,
        int $user_id = null,
        int $client_id = null
    )
    {
        $this->id=$id;
        $this->user_id=$user_id;
        $this->client_id=$client_id;
    }
    
    public function getUser() : ?User
    {
      return $this->user;
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
}