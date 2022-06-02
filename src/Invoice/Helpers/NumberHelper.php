<?php

declare(strict_types=1);

namespace App\Invoice\Helpers;

use App\Invoice\Entity\QuoteAmount;
use App\Invoice\Entity\InvAmount;
use App\Invoice\Setting\SettingRepository as SRepo;
use App\Invoice\QuoteItem\QuoteItemRepository as QIR;
use App\Invoice\InvItem\InvItemRepository as IIR;
use App\Invoice\QuoteAmount\QuoteAmountRepository as QAR;
use App\Invoice\InvAmount\InvAmountRepository as IAR;
use App\Invoice\Quote\QuoteRepository as QR;
use App\Invoice\Inv\InvRepository as IR;
use App\Invoice\QuoteItemAmount\QuoteItemAmountRepository as QIAR;
use App\Invoice\InvItemAmount\InvItemAmountRepository as IIAR;
use App\Invoice\QuoteTaxRate\QuoteTaxRateRepository as QTRR;
use App\Invoice\InvTaxRate\InvTaxRateRepository as ITRR;
use App\Invoice\Payment\PaymentRepository as PYMR;

Final Class NumberHelper
{

private SRepo $s;
    
public function __construct(SRepo $s)
{
    $this->s = $s;
}

public function format_currency($amount)
{
    $this->s->load_settings();
    $currency_symbol = $this->s->setting('currency_symbol');
    $currency_symbol_placement = $this->s->setting('currency_symbol_placement');
    $thousands_separator = $this->s->setting('thousands_separator');
    $decimal_point = $this->s->setting('decimal_point');
    if ($currency_symbol_placement == 'before') {
        return $currency_symbol. number_format((float)$amount, ($decimal_point) ? 2 : 0, $decimal_point, $thousands_separator);
    } elseif ($currency_symbol_placement == 'afterspace') {
        return number_format((float)$amount, ($decimal_point) ? 2 : 0, $decimal_point, $thousands_separator) . '&nbsp;' . $currency_symbol;
    } else {
        return number_format((float)$amount, ($decimal_point) ? 2 : 0, $decimal_point, $thousands_separator) . $currency_symbol;
    }
}

/**
 * Output the amount as a currency amount, e.g. 1.234,56
 *
 * @param null $amount
 * @return null|string
 */
public function format_amount($amount = null)
{
    $this->s->load_settings();
    if ($amount) {
        $thousands_separator = $this->s->setting('thousands_separator');
        $decimal_point = $this->s->setting('decimal_point');
        return number_format($amount, ($decimal_point) ? 2 : 0, $decimal_point, $thousands_separator);
    }
    return null;
}

/**
 * Standardize an amount based on the system settings
 *
 * @param $amount
 * @return mixed
 */
public function standardize_amount($amount)
{
    $this->s->load_settings();
    $thousands_separator = $this->s->setting('thousands_separator');
    $decimal_point = $this->s->setting('decimal_point');

    $amt = str_replace($thousands_separator, '', $amount);
    $final_amount = str_replace($decimal_point, '.', $amt);

    return $final_amount;
}

/**
 * @param $quote_id
 * @return float
 */
public function calculate_quote($quote_id, QIR $qiR, QIAR $qiaR, QTRR $qtrR, QAR $qaR, QR $qR) : void
{
        // Get all items that belong to a specific quote by accessing $qiR
        // Sum all these item's amounts 
        // -------------------------
        // Quote Subtotal + Item Tax
        // -------------------------    
        $quote_item_amounts = $this->quote_calculateTotalsofItemTotals($quote_id, $qiR, $qiaR);    
        $quote_item_subtotal_discount_inclusive = $quote_item_amounts['subtotal'] - $quote_item_amounts['discount'];
        $quote_subtotal_discount_and_tax_included = $quote_item_subtotal_discount_inclusive + $quote_item_amounts['tax_total'];
        //----------
        // Quote Tax
        // ---------
        $quote_tax_rate_total = $this->calculate_quote_taxes($quote_id, $qtrR, $qaR);
        //----------------------
        // Before Cash Discount
        // ---------------------
        $final_discountable_total = $quote_subtotal_discount_and_tax_included + $quote_tax_rate_total;
        //------------------------------------------------
        // Final Grand Total after Applying Cash Discount
        // -----------------------------------------------
        $quote_total = $this->quote_include_customer_discount_request($quote_id, $final_discountable_total, $qR);        
        
        //-----------------------------------------------------------------
        // Give the Quote its summary of amounts at the bottom of the quote
        //-----------------------------------------------------------------
        $count = $qiR->repoCount((string)$quote_id);
        $count_quote_amount = $qaR->repoQuoteAmountCount((string)$quote_id);
        //At least one item and a preexisting quote amount record exists => Update the Quote Amount Record
        if (($count > 0) && ($count_quote_amount > 0)) {
                $quote_amount = $qaR->repoQuotequery($quote_id);
                $quote_amount->setQuote_id((int)$quote_id);
                $quote_amount->setItem_subtotal($quote_item_subtotal_discount_inclusive ?? 0.00);
                $quote_amount->setItem_tax_total($quote_item_amounts['tax_total'] ?? 0.00);
                $quote_amount->setTax_total($quote_tax_rate_total ?? 0.00);
                $quote_amount->setTotal($quote_total ?? 0.00);
                $qaR->save($quote_amount);
        }
        // There are no longer any items on the quote so initialize the Quote Amount Record to zero
        if (($count === 0) && ($count_quote_amount > 0)) {
                $quote_amount = $qaR->repoQuotequery($quote_id);
                $quote_amount->setQuote_id((int)$quote_id);
                $quote_amount->setItem_subtotal(0.00);
                $quote_amount->setItem_tax_total(0.00);
                $quote_amount->setTax_total(0.00);
                $quote_amount->setTotal(0.00);
                $qaR->save($quote_amount);
        }
        if (($count === 0) && ($count_quote_amount === 0)) {
                // Create a Quote Amount Record for this quote if it does not exist even if there are no items
                $quote_amount = new QuoteAmount();
                $quote_amount->setQuote_id((int)$quote_id);
                $quote_amount->setItem_subtotal(0.00);
                $quote_amount->setItem_tax_total(0.00);
                $quote_amount->setTax_total(0.00);
                $quote_amount->setTotal(0.00);
                $qaR->save($quote_amount);
        }
    }
    
/**
 * @param $quote_id
 * @return float
 */
    
// Calculate the invoice's amounts totals from the items.
public function calculate_inv($inv_id, IIR $iiR, IIAR $iiaR, ITRR $itrR, IAR $iaR, IR $iR, PYMR $pymR) : void
    {
        // Get all items that belong to a specific invoice by accessing $iiR
        // Sum all these item's amounts 
        // -------------------------
        // Invoice Subtotal + Item Tax
        // -------------------------    
        $inv_item_amounts = $this->inv_calculateTotalsofItemTotals($inv_id, $iiR, $iiaR);    
        $inv_item_subtotal_discount_inclusive = $inv_item_amounts['subtotal'] - $inv_item_amounts['discount'];
        $inv_subtotal_discount_and_tax_included = $inv_item_subtotal_discount_inclusive + $inv_item_amounts['tax_total'];
        //----------
        // Invoice Tax
        // ---------
        $inv_tax_rate_total = $this->calculate_inv_taxes($inv_id, $itrR, $iaR);
        //----------------------
        // Before Cash Discount
        // ---------------------
        $final_discountable_total = $inv_subtotal_discount_and_tax_included + $inv_tax_rate_total;
        //------------------------------------------------
        // Final Grand Total after Applying Cash Discount
        // -----------------------------------------------
        $inv_total = $this->inv_include_customer_discount_request($inv_id, $final_discountable_total, $iR);        
        
        //-----------------------------------------------------------------
        // Give the Invoice its summary of amounts at the bottom of the quote
        //-----------------------------------------------------------------
        $count = $iiR->repoCount((string)$inv_id);
        $count_inv_amount = $iaR->repoInvAmountCount((string)$inv_id);
        //At least one item and a preexisting invoice amount record exists => Update the Invoice Amount Record
        if (($count > 0) && ($count_inv_amount > 0)) {
                $inv_amount = $iaR->repoInvquery((string)$inv_id);
                $inv_amount->setInv_id((int)$inv_id);
                $inv_amount->setItem_subtotal($inv_item_subtotal_discount_inclusive ?? 0.00);
                $inv_amount->setItem_tax_total($inv_item_amounts['tax_total'] ?? 0.00);
                $inv_amount->setTax_total($inv_tax_rate_total ?? 0.00);
                $inv_amount->setTotal($inv_total ?? 0.00);
                // The balance will be reduced with each payment
                $payments = ($pymR->repoCount((string)$inv_id) > 0 ? $pymR->repoInvquery((string)$inv_id) : []);
                $total_paid = (float)0.00;
                foreach ($payments as $payment) {
                    $paid = (float)$payment->getAmount();
                    $total_paid = $total_paid + $paid;                           
                }
                $inv_amount->setPaid($total_paid);
                $balance = $inv_total - $total_paid;
                $inv_amount->setBalance($balance);
                $iaR->save($inv_amount);
        }
        // There are no longer any items on the invoice so initialize the Invoice Amount Record to zero
        if (($count === 0) && ($count_inv_amount > 0)) {
                $inv_amount = $iaR->repoInvquery((string)$inv_id);
                $inv_amount->setInv_id((int)$inv_id);
                $inv_amount->setItem_subtotal(0.00);
                $inv_amount->setItem_tax_total(0.00);
                $inv_amount->setTax_total(0.00);
                $inv_amount->setTotal(0.00);
                $iaR->save($inv_amount);
        }
        if (($count === 0) && ($count_inv_amount === 0)) {
                // Create an Invoice  Amount Record for this invoice if it does not exist even if there are no items
                $inv_amount = new InvAmount();
                $inv_amount->setInv_id((int)$inv_id);
                $inv_amount->setItem_subtotal(0.00);
                $inv_amount->setItem_tax_total(0.00);
                $inv_amount->setTax_total(0.00);
                $inv_amount->setTotal(0.00);
                $iaR->save($inv_amount);
        }
    }
    
    /**
     * @param $quote_id
     * @return array
     */  
    
    private function quote_calculateTotalsofItemTotals($quote_id, QIR $qiR,QIAR $qiaR) : array { 
        $get_all_items_in_quote = $qiR->repoQuoteItemIdquery($quote_id);
        $grand_sub_total = 0.00;
        $grand_taxtotal = 0.00;
        $grand_discount = 0.00;
        $grand_total = 0.00;
        $totals = [
                'subtotal'=>$grand_sub_total,
                'tax_total'=>$grand_taxtotal,
                'discount'=>$grand_discount,
                'total'=>$grand_total,
        ];    
        foreach ($get_all_items_in_quote as $item){
            foreach ($item as $key => $value){
                if ($key === 'id'){
                   //use the id value to retrieve quote_item_amount subtotal 
                   $quote_item_amount = $qiaR->repoQuoteItemAmountquery((string)$value);
                   $grand_sub_total += $quote_item_amount->getSubTotal();
                   $grand_taxtotal += $quote_item_amount->getTax_total();
                   $grand_discount += $quote_item_amount->getDiscount();
                   $grand_total += $quote_item_amount->getTotal();
                }
            }
            $totals = [
                'subtotal'=>$grand_sub_total,
                'tax_total'=>$grand_taxtotal,
                'discount'=>$grand_discount,
                'total'=>$grand_total,
            ];
        }
        return $totals;
    }
    
    /**
     * @param $inv_id
     * @return array
     */  
    
    private function inv_calculateTotalsofItemTotals($inv_id, IIR $iiR, IIAR $iiaR) : array { 
        $get_all_items_in_inv = $iiR->repoInvItemIdquery((string)$inv_id);
        $grand_sub_total = 0.00;
        $grand_taxtotal = 0.00;
        $grand_discount = 0.00;
        $grand_total = 0.00;
        $totals = [
                'subtotal'=>$grand_sub_total,
                'tax_total'=>$grand_taxtotal,
                'discount'=>$grand_discount,
                'total'=>$grand_total,
        ];    
        foreach ($get_all_items_in_inv as $item){
            foreach ($item as $key => $value){
                if ($key === 'id'){
                   //use the id value to retrieve inv_item_amount subtotal 
                   $inv_item_amount = $iiaR->repoInvItemAmountquery((string)$value);
                   $grand_sub_total += $inv_item_amount->getSubTotal();
                   $grand_taxtotal += $inv_item_amount->getTax_total();
                   $grand_discount += $inv_item_amount->getDiscount();
                   $grand_total += $inv_item_amount->getTotal();
                }
            }
            $totals = [
                'subtotal'=>$grand_sub_total,
                'tax_total'=>$grand_taxtotal,
                'discount'=>$grand_discount,
                'total'=>$grand_total,
            ];
        }
        return $totals;
    }
    

    /**
     * @param $quote_id
     * @param $quote_total
     * @return float
     */
    public function quote_include_customer_discount_request($quote_id, $quote_total, QR $qR) : float
    {
        $quote = $qR->repoQuoteUnloadedquery($quote_id);

        $total = (float)number_format($quote_total, 2, '.', '');
        $discount_amount = (float)number_format($quote->getDiscount_amount(), 2, '.', '');
        $discount_percent = (float)number_format($quote->getDiscount_percent(), 2, '.', '');
        
        // Subtract Quote Table's discount amount from Quote Amount Table's quote_total
        // Discount and Percent are mutually exclusive ie. if you use the one you exclude the other.
        // Discount amount is the user inputed amount on the quote representing a cash discount
        // Discount percent is the user inputed percentage on the quote representing a cash percentage
        $total-= $discount_amount;        
        $total-= round(($total / 100 * $discount_percent), 2);        
        
        return $total;
    }
    
    /**
     * @param $inv_id
     * @param $invoice_total
     * @return float
     */
    public function inv_include_customer_discount_request($inv_id, $inv_total, IR $iR) : float
    {
        $inv = $iR->repoInvUnloadedquery((string)$inv_id);

        $total = (float)number_format($inv_total, 2, '.', '');
        $discount_amount = (float)number_format($inv->getDiscount_amount(), 2, '.', '');
        $discount_percent = (float)number_format($inv->getDiscount_percent(), 2, '.', '');
        
        // Subtract Invoice Table's discount amount from Invoice Amount Table's inv_total
        // Discount and Percent are mutually exclusive ie. if you use the one you exclude the other.
        // Discount amount is the user inputed amount on the invoice representing a cash discount
        // Discount percent is the user inputed percentage on the invoice representing a cash percentage
        $total-= $discount_amount;
        $total-= round(($total / 100 * $discount_percent), 2);

        return $total;
    }
    

    /**
     * @param $quote_id
     */
    public function calculate_quote_taxes($quote_id, QTRR $qtrR, QAR $qaR) : float
    {
        // Quote amount Table fields: id->quote_id->item_subtotal->item_tax_total->tax_total*->total
        // Quote Tax Rate Table fields: id->quote_id->tax_rate_id->include_item_tax->quote_tax_rate_amount*
        
        // Tax_total*    =    sum of quote_tax_rate_amount*   per   quote_id.
        
        // First check to see if there are any quote taxes applied
        $total_quote_tax_rate_amount = 0;
        $quote_tax_rates = $qtrR->repoQuotequery((string)$quote_id);

        // At least one quote tax rate has been set and the quote has amounts that quote tax rates can be applied to
        if ((!empty($quote_tax_rates)) && ($qaR->repoQuoteAmountCount((string)$quote_id)>0)) {
            // There are quote taxes applied
            
            $quote_amount = $qaR->repoQuotequery($quote_id);
            
            // Loop through the quote taxes and update quote_tax_rate_amount for each of the applied quote taxes
            foreach ($quote_tax_rates as $quote_tax_rate) {
                // If the include item tax has been checked
                $quote_tax_rate_amount = (
                        ($quote_tax_rate->getInclude_item_tax())
                        ? 
                            // The quote tax rate should include the applied item tax
                            (($quote_amount->getItem_subtotal() + $quote_amount->getItem_tax_total()) * ($quote_tax_rate->getTaxRate()->getTax_rate_percent() / 100))
                        :
                            // The quote tax rate should not include the applied item tax so get the general tax rate from Tax Rate table
                            ($quote_amount->getItem_subtotal() * ($quote_tax_rate->getTaxRate()->getTax_rate_percent() / 100))
                ); 
                // Update the quote tax rate amount                    
                $quote_tax_rate->setQuote_tax_rate_amount($quote_tax_rate_amount);
                $qtrR->save($quote_tax_rate);
                $total_quote_tax_rate_amount += $quote_tax_rate_amount;
            }         
        }
        return $total_quote_tax_rate_amount;    
    }

    /**
     * @param $inv_id
     */
    public function calculate_inv_taxes($inv_id, ITRR $itrR, IAR $iaR) : float
    {
        // Invoiec amount Table fields: id->inv_id->item_subtotal->item_tax_total->tax_total*->total
        // Invoice Tax Rate Table fields: id->inv_id->tax_rate_id->include_item_tax->inv_tax_rate_amount*
        
        // Tax_total*    =    sum of inv_tax_rate_amount*   per   inv_id.
        
        // First check to see if there are any invoice taxes applied
        $total_inv_tax_rate_amount = 0;
        $inv_tax_rates = $itrR->repoInvquery((string)$inv_id);

        // At least one invoice tax rate has been set and the invoice has amounts that invoice tax rates can be applied to
        if ((!empty($inv_tax_rates)) && ($iaR->repoInvAmountCount((string)$inv_id)>0)) {
            // There are invoice taxes applied
            
            $inv_amount = $iaR->repoInvquery((string)$inv_id);
            
            // Loop through the invoice taxes and update inv_tax_rate_amount for each of the applied inv taxes
            foreach ($inv_tax_rates as $inv_tax_rate) {
                // If the include item tax has been checked
                $inv_tax_rate_amount = (
                        ($inv_tax_rate->getInclude_item_tax())
                        ? 
                            // The inv tax rate should include the applied item tax
                            (($inv_amount->getItem_subtotal() + $inv_amount->getItem_tax_total()) * ($inv_tax_rate->getTaxRate()->getTax_rate_percent() / 100))
                        :
                            // The invoice tax rate should not include the applied item tax so get the general tax rate from Tax Rate table
                            ($inv_amount->getItem_subtotal() * ($inv_tax_rate->getTaxRate()->getTax_rate_percent() / 100))
                ); 
                // Update the invoice tax rate amount                    
                $inv_tax_rate->setInv_tax_rate_amount($inv_tax_rate_amount);
                $itrR->save($inv_tax_rate);
                $total_inv_tax_rate_amount += $inv_tax_rate_amount;
            }         
        }
        return $total_inv_tax_rate_amount;    
    }

    /**
     * @param null $period
     * @return mixed
     */
    public function get_total_quoted(QR $qR,$period = null)
    {
        $sql = "SUM(quote_total) AS total_quoted";
        switch ($period) {
            case 'month':
                return $this->select($sql)
                            ->where(['quote_id','in'=>[$qR->repoQuoteMonth()]])
                            ->total_quoted;
            case 'last_month':
                return $this->select($sql)
                            ->where(['quote_id','in'=>[$qR->repoQuoteLastMonth()]])
                            ->total_quoted;
            case 'year':
                return $this->select($sql)
                            ->where(['quote_id','in'=>[$qR->repoQuoteYear()]])
                            ->total_quoted;
            case 'last_year':
                return $this->select($sql)
                            ->where(['quote_id','in'=>[$qR->repoQuoteLastYear()]])
                            ->total_quoted;
            default:
                return $this->select($sql)
                            ->total_quoted;
        }
    }
    
    /**
     * @param null $period
     * @return mixed
     */
    public function get_total_inved(IR $iR,$period = null)
    {
        $sql = "SUM(invoice_total) AS total_invoiced";
        switch ($period) {
            case 'month':
                return $this->select($sql)
                            ->where(['inv_id','in'=>[$iR->repoInvMonth()]])
                            ->total_inved;
            case 'last_month':
                return $this->select($sql)
                            ->where(['inv_id','in'=>[$iR->repoInvLastMonth()]])
                            ->total_inved;
            case 'year':
                return $this->select($sql)
                            ->where(['inv_id','in'=>[$iR->repoInvYear()]])
                            ->total_inved;
            case 'last_year':
                return $this->select($sql)
                            ->where(['inv_id','in'=>[$iR->repoInvLastYear()]])
                            ->total_inved;
            default:
                return $this->select($sql)
                            ->total_inved;
        }
    }
    
    public function recur_frequencies() 
    { 
        $recur_frequencies = [
            '1D' => 'calendar_day_1',
            '2D' => 'calendar_day_2',
            '3D' => 'calendar_day_3',
            '4D' => 'calendar_day_4',
            '5D' => 'calendar_day_5',
            '6D' => 'calendar_day_6',
            '15D' => 'calendar_day_15',
            '30D' => 'calendar_day_30',
            '7D' => 'calendar_week_1',
            '14D' => 'calendar_week_2',
            '21D' => 'calendar_week_3',
            '28D' => 'calendar_week_4',
            '1M' => 'calendar_month_1',
            '2M' => 'calendar_month_2',
            '3M' => 'calendar_month_3',
            '4M' => 'calendar_month_4',
            '5M' => 'calendar_month_5',
            '6M' => 'calendar_month_6',
            '7M' => 'calendar_month_7',
            '8M' => 'calendar_month_8',
            '9M' => 'calendar_month_9',
            '10M' => 'calendar_month_10',
            '11M' => 'calendar_month_11',
            '1Y' => 'calendar_year_1',
            '2Y' => 'calendar_year_2',
            '3Y' => 'calendar_year_3',
            '4Y' => 'calendar_year_4',
            '5Y' => 'calendar_year_5',
        ];
        return $recur_frequencies;
    }

}