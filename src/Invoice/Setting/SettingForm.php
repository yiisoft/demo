<?php

declare(strict_types=1);

namespace App\Invoice\Setting;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;

final class SettingForm extends FormModel
{
    private ?string $setting_key = null;
    private ?string $setting_value = null;
        
    public function getSetting_key(): string
    {
        return $this->setting_key;
    }

    public function getSetting_value(): string
    {
        return $this->setting_value;
    }
    
       
    public function getFormName(): string
    {
        return '';
    }
    
    public function getRules(): array
    {
        return [
            'setting_key' => [
                Required::rule(),
            ],
            'setting_value' => [
                Required::rule(),
            ],
        ];
    }
}
