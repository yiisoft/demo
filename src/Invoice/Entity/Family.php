<?php

declare(strict_types=1);

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

/**
 * @Entity(
 *     repository="App\Invoice\Family\FamilyRepository",
 * )
 */
class Family
{
    /**
     * @Column(type="primary")
     */
    public ?int $id = null;
    
    /**
     * @Column(type="text", nullable=true)
     */
    public ?string $family_name = null;
    
    public function __construct(
            string $family_name=''
    )
    {
        $this->family_name = $family_name;
    }
    
    public function getFamily_id(): ?int
    {
        return $this->id;
    }

    public function getFamily_name(): string
    {
        return $this->family_name;
    }

    public function setFamily_name(string $family_name): void
    {
        $this->family_name = $family_name;
    }
}
