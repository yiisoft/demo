<?php

declare(strict_types=1); 

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use App\Invoice\Entity\User;

#[Entity(repository: \App\Invoice\UserCustom\UserCustomRepository::class)]
class UserCustom
{
    #[BelongsTo(target: \App\User\User::class, nullable: false)]
    private ?User $user = null;
    
    #[Column(type: 'primary')]
    private ?int $id =  null;
     
    #[Column(type: 'integer(11)', nullable: false)]
    private ?int $user_id =  null;
     
    #[Column(type: 'integer(11)', nullable: false)]
    private ?int $fieldid =  null;
     
    #[Column(type: 'text', nullable: true)]
    private ?string $fieldvalue =  '';
     
    public function __construct(
        int $id = null,
        int $user_id = null,
        int $fieldid = null,
        string $fieldvalue = ''
    )
    {
        $this->id=$id;
        $this->user_id=$user_id;
        $this->fieldid=$fieldid;
        $this->fieldvalue=$fieldvalue;
    }
    
    public function getUser() : ?User
    {
      return $this->user;
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