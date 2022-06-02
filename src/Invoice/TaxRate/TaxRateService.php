<?php

declare(strict_types=1);

namespace App\Invoice\TaxRate;

use App\Invoice\Entity\TaxRate;

final class TaxRateService
{
    private TaxRateRepository $repository;

    public function __construct(TaxRateRepository $repository)
    {
        $this->repository = $repository;
    }

    public function saveTaxRate(TaxRate $model, TaxRateForm $form): void
    {
        $model->setTax_rate_name($form->getTax_rate_name());
        $model->setTax_rate_percent($form->getTax_rate_percent());
        $model->setTax_rate_default($form->getTax_rate_default());        
        
        if ($model->isNewRecord()) {
            $model->setTax_rate_default(false);
        }
        
        $this->repository->save($model);
    }
    
    public function deleteTaxRate(TaxRate $model): void
    {
        $this->repository->delete($model);
    }
}
