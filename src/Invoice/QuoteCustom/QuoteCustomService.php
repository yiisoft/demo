<?php

declare(strict_types=1); 

namespace App\Invoice\QuoteCustom;

use App\Invoice\Entity\QuoteCustom;


final class QuoteCustomService
{
    private QuoteCustomRepository $repository;

    public function __construct(QuoteCustomRepository $repository)
    {
        $this->repository = $repository;
    }

    public function saveQuoteCustom(QuoteCustom $model, QuoteCustomForm $form): void
    { 
       $model->setQuote_id($form->getQuote_id());
       $model->setCustom_field_id($form->getCustom_field_id());
       $model->setValue($form->getValue());
       $this->repository->save($model);
    }
    
    public function deleteQuoteCustom(QuoteCustom $model): void
    {
        $this->repository->delete($model);
    }
}