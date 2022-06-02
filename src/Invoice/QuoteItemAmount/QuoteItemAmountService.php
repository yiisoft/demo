<?php

declare(strict_types=1); 

namespace App\Invoice\QuoteItemAmount;

use App\Invoice\Entity\QuoteItemAmount;


final class QuoteItemAmountService
{

    private QuoteItemAmountRepository $repository;

    public function __construct(QuoteItemAmountRepository $repository)
    {
        $this->repository = $repository;
    }

    public function saveQuoteItemAmount(QuoteItemAmount $model, QuoteItemAmountForm $form): void
    { 
       $model->setQuote_item_id($form->getQuote_item_id());
       $model->setSubtotal($form->getSubtotal());
       $model->setTax_total($form->getTax_total());
       $model->setDiscount($form->getDiscount());
       $model->setTotal($form->getTotal());
       $this->repository->save($model);
    }
    
    public function saveQuoteItemAmountNoForm(QuoteItemAmount $model, $quoteitem): void
    {        
       $model->setQuote_item_id((int)$quoteitem['quote_item_id']);
       $model->setSubtotal($quoteitem['subtotal']);
       $model->setTax_total($quoteitem['taxtotal']);
       $model->setDiscount($quoteitem['discount']);
       $model->setTotal($quoteitem['total']); 
       $this->repository->save($model);
    }
    
    public function deleteQuoteItemAmount(QuoteItemAmount $model): void
    {
       $this->repository->delete($model);
    }
}