<?php

declare(strict_types=1);

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

#[Entity(repository: \App\Invoice\TaxRate\TaxRateRepository::class)]
class TaxRate
{
    #[Column(type: 'primary')]
    private ?int $id = null;
    
    #[Column(type: 'text', nullable: true)]
    private ?string $tax_rate_name = null;
    
    #[Column(type: 'decimal(5,2)')]
    private ?float $tax_rate_percent = null;
    
    #[Column(type: 'bool', default:false)]
    private ?bool $tax_rate_default = false;
    
    public function __construct(
        string $tax_rate_name='',
        float $tax_rate_percent=null,
        bool $tax_rate_default=false
    )
    {
        $this->tax_rate_name = $tax_rate_name;
        $this->tax_rate_percent = $tax_rate_percent;
        $this->tax_rate_default = $tax_rate_default;
    }
    
    public function getTax_rate_id(): ?int
    {
        return $this->id;
    }

    public function getTax_rate_name(): string
    {
        return $this->tax_rate_name;
    }

    public function setTax_rate_name(string $tax_rate_name): void
    {
        $this->tax_rate_name = $tax_rate_name;
    }
    
    public function getTax_rate_percent(): float
    {
        return $this->tax_rate_percent;
    }
    
    public function setTax_rate_percent(float $tax_rate_percent): void
    {
        $this->tax_rate_percent = $tax_rate_percent; 
    }
    
    public function getTax_rate_default(): bool
    {
        return $this->tax_rate_default;
    }
    
    public function setTax_rate_default(bool $tax_rate_default): void 
    {
        $this->tax_rate_default = $tax_rate_default;
    }
    
    public function isNewRecord(): bool
    {
        return $this->getTax_rate_id() === null;
    }
}
