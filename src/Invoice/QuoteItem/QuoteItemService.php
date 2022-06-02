<?php

declare(strict_types=1); 

namespace App\Invoice\QuoteItem;

use App\Invoice\Entity\QuoteItem;
use App\Invoice\Entity\QuoteItemAmount;
use App\Invoice\QuoteItemAmount\QuoteItemAmountService as QIAS;

use App\Invoice\TaxRate\TaxRateRepository as TRR;
use App\Invoice\Product\ProductRepository as PR;
use App\Invoice\QuoteItemAmount\QuoteItemAmountRepository as QIAR;
use App\Invoice\Unit\UnitRepository as UR;

final class QuoteItemService
{
    private QuoteItemRepository $repository;    

    public function __construct(QuoteItemRepository $repository)
    {
        $this->repository = $repository;
    }

    public function saveQuoteItem(QuoteItem $model, QuoteItemForm $form, $quote_id,PR $pr, TRR $trr , QIAS $qias, QIAR $qiar, UR $uR): void
    {        
       $model->setQuote_id((int)$quote_id);
       
       // The form is required to have a tax value even if it is a zero rate
       $model->setTax_rate_id($form->getTax_rate_id());
       
       $product_id = ((null !==($form->getProduct_id())) ? $form->getProduct_id() : '');
       $model->setProduct_id($product_id);
       
       $name = (( (null !==($form->getProduct_id())) && ($pr->repoCount($product_id)> 0) ) ? $pr->repoProductquery((string)$form->getProduct_id())->getProduct_name() : '');  
       $model->setName($name);
       
       // If the user has changed the description on the form => override default product description
       $description = ((null !==($form->getDescription())) ? 
                                 $form->getDescription() : 
                                 $pr->repoProductquery((string)$form->getProduct_id())->getProduct_description());
       
       $model->setDescription($description);
       
       $model->setQuantity($form->getQuantity());
       
       $model->setPrice($form->getPrice());
       $model->setDiscount_amount($form->getDiscount_amount());
       $model->setOrder($form->getOrder());
       // Product_unit is a string which we get from unit's name field using the unit_id
       $model->setProduct_unit($uR->repoUnitquery((string)$form->getProduct_unit_id())->getUnit_name());
       $model->setProduct_unit_id($form->getProduct_unit_id());
       
       // Users are required to enter a tax rate even if it is zero percent.
       $tax_rate_id = $form->getTax_rate_id();
       $tax_rate_percentage = $this->taxrate_percentage($tax_rate_id, $trr);
       
       $product_id ? $this->repository->save($model) : '';  
       
       $product_id ? $this->saveQuoteItemAmount($model->getId(), $form->getQuantity(), $form->getPrice(), $form->getDiscount_amount(), $tax_rate_percentage, $qias, $qiar) : '';
    }     
    
    public function saveQuoteItemAmount($quote_item_id, $quantity, $price, $discount, $tax_rate_percentage, QIAS $qias, QIAR $qiar)
    {
       
       $qias_array['quote_item_id'] = $quote_item_id;
       
       $sub_total = (float)($quantity * $price);
       $tax_total = (float)(($sub_total * ($tax_rate_percentage/100)));
       $discount_total = (float)($quantity*$discount);
       
       $qias_array['discount'] = $discount_total;
       $qias_array['subtotal'] = $sub_total;
       $qias_array['taxtotal'] = $tax_total;
       $qias_array['total'] = (float)($sub_total - $discount_total + $tax_total);       
       
       if ($qiar->repoCount($quote_item_id) === 0) {
         $qias->saveQuoteItemAmountNoForm(new QuoteItemAmount() , $qias_array);} else {
         $qias->saveQuoteItemAmountNoForm($qiar->repoQuoteItemAmountquery($quote_item_id) , $qias_array);     
       }                      
    }        
    
    public function deleteQuoteItem(QuoteItem $model): void 
    {
        $this->repository->delete($model);
    }
    
    public function taxrate_percentage($id, TRR $trr)
    {
        $taxrate = $trr->repoTaxRatequery((string)$id);
        $percentage = $taxrate->getTax_rate_percent();        
        return $percentage;
    }
}