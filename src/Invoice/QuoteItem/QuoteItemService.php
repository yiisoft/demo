<?php

declare(strict_types=1); 

namespace App\Invoice\QuoteItem;

use App\Invoice\Entity\QuoteItem;


final class QuoteItemService
{

    private QuoteItemRepository $repository;

    public function __construct(QuoteItemRepository $repository)
    {
        $this->repository = $repository;
    }

    public function saveQuoteItem(QuoteItem $model, QuoteItemForm $form): void
    {
        
       $model->setQuote_id($form->getQuote_id());
       $model->setTax_rate_id($form->getTax_rate_id());
       $model->setProduct_id($form->getProduct_id());
       $model->setDate_added($form->getDate_added());
       $model->setName($form->getName());
       $model->setDescription($form->getDescription());
       $model->setQuantity($form->getQuantity());
       $model->setPrice($form->getPrice());
       $model->setDiscount_amount($form->getDiscount_amount());
       $model->setOrder($form->getOrder());
       $model->setProduct_unit($form->getProduct_unit());
       $model->setProduct_unit_id($form->getProduct_unit_id());
 
        $this->repository->save($model);
    }
    
    public function deleteQuoteItem(QuoteItem $model): void
    {
        $this->repository->delete($model);
    }
}