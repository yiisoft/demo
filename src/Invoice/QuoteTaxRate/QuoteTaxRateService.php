<?php

declare(strict_types=1); 

namespace App\Invoice\QuoteTaxRate;

use App\Invoice\Entity\QuoteTaxRate;


final class QuoteTaxRateService
{

    private QuoteTaxRateRepository $repository;

    public function __construct(QuoteTaxRateRepository $repository)
    {
        $this->repository = $repository;
    }

    public function saveQuoteTaxRate(QuoteTaxRate $model, QuoteTaxRateForm $form): void
    {
        
       $model->setQuote_id($form->getQuote_id());
       $model->setTax_rate_id($form->getTax_rate_id());
       $model->setInclude_item_tax($form->getInclude_item_tax());
       $model->setQuote_tax_rate_amount($form->getQuote_tax_rate_amount());
 
        $this->repository->save($model);
    }
    
    public function deleteQuoteTaxRate(QuoteTaxRate $model): void
    {
        $this->repository->delete($model);
    }
}