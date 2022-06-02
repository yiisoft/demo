<?php
declare(strict_types=1); 

namespace App\Invoice\InvItem;

use App\Invoice\Entity\InvItem;
use App\Invoice\Entity\InvItemAmount;
use App\Invoice\Helpers\DateHelper;
use App\Invoice\InvItemAmount\InvItemAmountRepository as IIAR;
use App\Invoice\InvItemAmount\InvItemAmountService as IIAS;
use App\Invoice\Product\ProductRepository as PR;
use App\Invoice\Setting\SettingRepository as SR;
use App\Invoice\TaxRate\TaxRateRepository as TRR;
use App\Invoice\Unit\UnitRepository as UNR;

final class InvItemService
{
    private InvItemRepository $repository;
 
    public function __construct(InvItemRepository $repository)
    {
        $this->repository = $repository;
    }

    public function saveInvItem(InvItem $model, InvItemForm $form, $inv_id,PR $pr, TRR $trr , IIAS $iias, IIAR $iiar, UNR $unR): void
    {        
       $model->setInv_id((int)$inv_id);
       
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
       $model->setDate($form->getDate());
       
       // Product_unit is a string which we get from unit's name field using the unit_id
       $model->setProduct_unit($unR->repoUnitquery((string)$form->getProduct_unit_id())->getUnit_name());
       $model->setProduct_unit_id($form->getProduct_unit_id());
       
       // Users are required to enter a tax rate even if it is zero percent.
       $tax_rate_id = $form->getTax_rate_id();
       $tax_rate_percentage = $this->taxrate_percentage($tax_rate_id, $trr);
       
       if ($model->isNewRecord()) {
           $model->setDate('0000-00-00');
       }
       
       $product_id ? $this->repository->save($model) : '';  
       
       $product_id ? $this->saveInvItemAmount($model->getId(), $form->getQuantity(), $form->getPrice(), $form->getDiscount_amount(), $tax_rate_percentage, $iias, $iiar) : '';
    }     
    
    public function saveInvItemAmount($inv_item_id, $quantity, $price, $discount, $tax_rate_percentage, IIAS $iias, IIAR $iiar)
    {       
       $iias_array['inv_item_id'] = $inv_item_id;       
       $sub_total = (float)($quantity * $price);
       $tax_total = (float)(($sub_total * ($tax_rate_percentage/100)));
       $discount_total = (float)($quantity*$discount);
       
       $iias_array['discount'] = $discount_total;
       $iias_array['subtotal'] = $sub_total;
       $iias_array['taxtotal'] = $tax_total;
       $iias_array['total'] = (float)($sub_total - $discount_total + $tax_total);       
       
       if ($iiar->repoCount($inv_item_id) === 0) {
         $iias->saveInvItemAmountNoForm(new InvItemAmount(),$iias_array);} else {
         $iias->saveInvItemAmountNoForm($iiar->repoInvItemAmountquery($inv_item_id),$iias_array);     
       }                      
    }        
    
    public function deleteInvItem(InvItem $model): void 
    {
        $this->repository->delete($model);
    }
    
    public function taxrate_percentage($id, TRR $trr)
    {
        $taxrate = $trr->repoTaxRatequery((string)$id);
        $percentage = $taxrate->getTax_rate_percent();        
        return $percentage;
    }
    
    public function initializeCreditInvItems($basis_inv_id, $new_inv_id, InvItemRepository $iiR, IIAR $iiaR, SR $sR) {        
        // Get the basis invoice's items and balance with a negative quantity
        $items = $iiR->repoInvquery($basis_inv_id);
        foreach ($items as $item){
            $new_item = new InvItem();
            $new_item->setInv_id((int)$new_inv_id);
            $new_item->setTax_rate_id((int)$item->getTax_rate_id());
            $new_item->setProduct_id((int)$item->getProduct_id());
            $new_item->setName($item->getName() ?? '');
            $new_item->setDescription($item->getDescription() ?? '');
            $new_item->setQuantity($item->getQuantity()*-1);
            $new_item->setPrice($item->getPrice() ?? 0.00);
            $new_item->setDiscount_amount($item->getDiscount_amount() ?? 0.00);
            $new_item->setOrder($item->getOrder());
            // Even if an invoice is balanced with a credit invoice it will remain recurring ... unless stopped.
            $new_item->setIs_recurring($item->getIs_recurring());
            $new_item->setProduct_unit($item->getProduct_unit());
            $new_item->setProduct_unit_id((int)$item->getProduct_unit_id());
            //$new_item->setDate($datehelper->getYear_from_DateTime($item->getDate()) === '-0001' ? null : DateTime($datehelper->date_from_mysql($item->getDate())));
            $iiR->save($new_item);
                       
            // Create an item amount for this item; reversing the items amounts to negative
            $basis_item_amount = $iiaR->repoInvItemAmountquery($item->getId());
            $new_item_amount = new InvItemAmount();
            $new_item_amount->setInv_item_id((int)$new_item->getId());
            $new_item_amount->setSubtotal($basis_item_amount->getSubtotal()*-1);
            $new_item_amount->setTax_total($basis_item_amount->getTax_total()*-1);
            $new_item_amount->setDiscount($basis_item_amount->getDiscount()*-1);
            $new_item_amount->setTotal($basis_item_amount->getTotal()*-1);
            $iiaR->save($new_item_amount);        
        }
    }    
}