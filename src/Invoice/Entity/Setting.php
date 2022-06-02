<?php

declare(strict_types=1);

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

#[Entity(repository: \App\Invoice\Setting\SettingRepository::class)]
class Setting
{
    #[Column(type: 'primary')]
    private ?int $id = null;
    
    #[Column(type: 'string(50)')]
    private string $setting_key = '';
    
    #[Column(type: 'longText')]
    private string $setting_value = '';
    
    #[Column(type: 'string(30)')]
    private string $setting_trans = '';
    
    #[Column(type: 'string(30)')]
    private string $setting_section = '';
    
    #[Column(type: 'string(30)')]
    private string $setting_subsection = '';
        
    public function __construct(
            string $setting_key='',
            string $setting_value='',
            string $setting_trans='',
            string $setting_section='',
            string $setting_subsection=''
    )
    {
        $this->setting_key = $setting_key;
        $this->setting_value = $setting_value;
        $this->setting_trans = $setting_trans;
        $this->setting_section = $setting_section;
        $this->setting_subsection = $setting_subsection;
    }
    
    public function getSetting_id(): ?int
    {
        return $this->id;
    }

    public function getSetting_key(): string
    {
        return $this->setting_key;
    }

    public function setSetting_key(string $setting_key): void
    {
        $this->setting_key = $setting_key;
    }
    
    public function getSetting_value(): string
    {
        return $this->setting_value;
    }
    
    public function setSetting_value(string $setting_value): void
    {
        $this->setting_value = $setting_value;
    }

    public function setSetting_trans(string $setting_trans): void
    {
        $this->setting_trans = $setting_trans;
    }
    
    public function getSetting_trans(): string
    {
        return $this->setting_trans;
    }
    
    public function setSetting_section(string $setting_section): void
    {
        $this->setting_section = $setting_section;
    }
    
    public function getSetting_section(): string
    {
        return $this->setting_section;
    }
    
    public function setSetting_subsection(string $setting_subsection): void
    {
        $this->setting_subsection = $setting_subsection;
    }
    
    public function getSetting_subsection(): string
    {
        return $this->setting_subsection;
    }
}
