<?php

declare(strict_types=1);

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

/**
 * @Entity(
 *     repository="App\Invoice\TaxRate\TaxRateRepository",
 * )
 */
class TaxRate
{
    /**
     * @Column(type="primary")
     */
    public ?int $id = null;
    
    /**
     * @Column(type="text", nullable=true)
     */
    public ?string $tax_rate_name = null;
    
    /**
     * @Column(type="decimal(5,2)")
     */
    public ?float $tax_rate_percent = null;
    
    public function __construct(
            string $tax_rate_name='',
            float $tax_rate_percent=null            
    )
    {
        $this->tax_rate_name = $tax_rate_name;
        $this->tax_rate_percent = $tax_rate_percent;
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
}
