<?php

declare(strict_types=1);

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use App\Invoice\Entity\Client;
use Cycle\Annotated\Annotation\Relation\BelongsTo;

#[Entity(repository: \App\Invoice\Project\ProjectRepository::class)]
class Project
{
    #[Column(type: 'primary')]
    private ?int $id = null;
    
    #[BelongsTo(target:Client::class, nullable: false, fkAction: 'NO ACTION')]
    private ?Client $client = null;
        
    #[Column(type: 'integer(11)', nullable:false)]
    private ?int $client_id = null;
        
    #[Column(type: 'text', nullable: true)]
    private ?string $name = null;
    
    public function __construct(
        int $client_id=null,
        string $name=''            
    )
    {
        $this->client_id = $client_id;
        $this->name = $name;
    }
    
    public function getId(): ?string
    {
        return (string)$this->id;
    }
        
    public function getClient(): ?Client
    {
        return $this->client;
    }
    
    public function setClient_id(int $client_id): void
    {
        $this->client_id = $client_id;
    }

    public function getClient_id(): ?int
    {
        return $this->client_id;
    }
    
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
