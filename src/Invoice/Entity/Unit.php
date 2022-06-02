<?php

declare(strict_types=1);

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

#[Entity(repository: \App\Invoice\Unit\UnitRepository::class)]
class Unit
{    
    #[Column(type: 'primary')]
    public ?int $id = null;
    
    #[Column(type: 'string(50)')]
    private string $unit_name = '';
    
    #[Column(type: 'string(50)')]
    private string $unit_name_plrl = '';
        
    public function __construct(
        string $unit_name='',
        string $unit_name_plrl=''
    )
    {
        $this->unit_name = $unit_name;
        $this->unit_name_plrl = $unit_name_plrl;
    }
    
    public function getUnit_id(): ?int
    {
        return $this->id;
    }

    public function getUnit_name(): string
    {
        return $this->unit_name;
    }

    public function setUnit_name(string $unit_name): void
    {
        $this->unit_name = $unit_name;
    }
    
    public function getUnit_name_plrl(): string
    {
        return $this->unit_name_plrl;
    }

    public function setUnit_name_plrl(string $unit_name_plrl): void
    {
        $this->unit_name_plrl = $unit_name_plrl;
    }
}
