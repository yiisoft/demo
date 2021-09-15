<?php

declare(strict_types=1); 

namespace App\Invoice\QuoteAmount;

use App\Invoice\Entity\QuoteAmount;


final class QuoteAmountService
{

    private QuoteAmountRepository $repository;

    public function __construct(QuoteAmountRepository $repository)
    {
        $this->repository = $repository;
    }

    public function saveQuoteAmount(QuoteAmount $model, QuoteAmountForm $form): void
    {
        
       $model->setQuote_id($form->getQuote_id());
       $model->setItem_subtotal($form->getItem_subtotal());
       $model->setItem_tax_total($form->getItem_tax_total());
       $model->setTax_total($form->getTax_total());
       $model->setTotal($form->getTotal());
 
        $this->repository->save($model);
    }
    
    public function deleteQuoteAmount(QuoteAmount $model): void
    {
        $this->repository->delete($model);
    }
}