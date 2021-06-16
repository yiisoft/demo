<?php

declare(strict_types=1);

namespace App\Invoice\TaxRate;

use Yiisoft\Form\FormModel;

final class TaxRateForm extends FormModel
{
    private ?string $tax_rate_name = null;
    
    private ?float $tax_rate_percent = 0.00;
    
    public function getTax_rate_name(): string
    {
        return $this->tax_rate_name;
    }
    
    public function getTax_rate_percent() : float
    {
        return $this->tax_rate_percent;
    }
    
    public function getFormName(): string
    {
        return '';
    }
}
