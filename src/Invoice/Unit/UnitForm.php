<?php

declare(strict_types=1);

namespace App\Invoice\Unit;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;

final class UnitForm extends FormModel
{
    private ?string $unit_name = null;
    private ?string $unit_name_plrl = null;
        
    public function getUnit_name(): string
    {
        return $this->unit_name;
    }

    public function getUnit_name_plrl(): string
    {
        return $this->unit_name_plrl;
    }
    
       
    public function getFormName(): string
    {
        return '';
    }
    
    public function getRules(): array
    {
        return [
            'unit_name' => [new Required()],
            'unit_name_plrl' => [new Required()],
        ];
    }
}
