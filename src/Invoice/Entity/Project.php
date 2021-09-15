<?php

declare(strict_types=1);

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use App\Invoice\Entity\Client;
use Cycle\Annotated\Annotation\Relation\BelongsTo;

/**
 * @Entity(
 *     repository="App\Invoice\Project\ProjectRepository",
 * )
 */
class Project
{
    /**
     * @Column(type="primary")
     */
    public ?int $id = null;
    
    /**
     * @BelongsTo(target="Client", nullable=false, fkAction="NO ACTION")
     *
     * @var \Cycle\ORM\Promise\Reference|Client
     */
    private $client = null;
    
    /**
     * @Column(type="integer(11)", nullable=false)
     */
    private ?int $client_id = null;
    
    /**
     * @Column(type="text", nullable=true)
     */
    public ?string $project_name = null;
    
    public function __construct(
            int $client_id=null,
            string $project_name=''            
    )
    {
        $this->client_id = $client_id;
        $this->project_name = $project_name;
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
    
    public function getProject_id(): ?int
    {
        return $this->id;
    }
    
    public function getProject_name(): string
    {
        return $this->project_name;
    }

    public function setProject_name(string $project_name): void
    {
        $this->project_name = $project_name;
    }
}
