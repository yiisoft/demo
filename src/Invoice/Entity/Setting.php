<?php

declare(strict_types=1);

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

/**
 * @Entity(
 *     repository="App\Invoice\Setting\SettingRepository",
 * )
 */
class Setting
{
    /**
     * @Column(type="primary")
     */
    public ?int $id = null;
    
    /**
     * @Column(type="string(50)")
     */
    public string $setting_key = '';
    
    /**
     * @Column(type="longText")
     */
    public string $setting_value = '';
        
    public function __construct(
            string $setting_key='',
            string $setting_value=''
    )
    {
        $this->setting_key = $setting_key;
        $this->setting_value = $setting_value;
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
}
