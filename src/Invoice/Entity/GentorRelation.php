<?php

declare(strict_types=1);

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use App\Invoice\Entity\Gentor;
use Cycle\Annotated\Annotation\Relation\BelongsTo;

/**
 * @Entity(
 *     repository="App\Invoice\GeneratorRelation\GeneratorRelationRepository",
 * )
 */
class GentorRelation
{
    /**
     * @Column(type="primary")
     */
    public ?int $id = null;
    
    /**
     * @Column(type="text", nullable=true)
     */
    public ?string $lowercasename = null;
    
    /**
     * @Column(type="text", nullable=true)
     */
    public ?string $camelcasename = null;
    
    /**
     * @BelongsTo(target="Gentor", nullable=false, fkAction="NO ACTION")
     *
     * @var \Cycle\ORM\Promise\Reference|Gentor
     */
    private $gentor = null;
    
    /**
     * @Column(type="integer(11)", nullable=true, default=null)
     */
    private ?int $gentor_id = null;
    
    public function __construct(
            string $lowercasename='',
            string $camelcasename='',
            int $gentor_id=null
    )
    {
        $this->lowercasename = $lowercasename;
        $this->camelcasename = $camelcasename;
        $this->gentor_id = $gentor_id;
    }
    
    public function getRelation_id(): ?string
    {
        return (string)$this->id;
    }
    
    //relation $gentor
    public function getGentor()
    {
        return $this->gentor;
    }

    public function getLowercase_name(): string
    {
        return $this->lowercasename;
    }

    public function setLowercase_name(string $lowercasename): void
    {
        $this->lowercasename = $lowercasename;
    }
    
    public function getCamelcase_name(): string
    {
        return $this->camelcasename;
    }

    public function setCamelcase_name(string $camelcasename): void
    {
        $this->camelcasename = $camelcasename;
    }
    
    public function getGentor_id(): int
    {
        return $this->gentor_id;
    }

    public function setGentor_id(int $gentor_id): void
    {
        $this->gentor_id = $gentor_id;
    }
}
