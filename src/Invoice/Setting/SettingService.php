<?php

declare(strict_types=1);

namespace App\Invoice\Setting;

use App\Invoice\Entity\Setting;

final class SettingService
{
    private SettingRepository $repository;

    public function __construct(SettingRepository $repository)
    {
        $this->repository = $repository;
    }

    public function saveSetting(Setting $model, SettingForm $form): void
    {
        $model->setSetting_key($form->getSetting_key());
        $model->setSetting_value($form->getSetting_value());
        $this->repository->save($model);
    }
    
    public function deleteSetting(Setting $model): void
    {
        $this->repository->delete($model);
    }
}
