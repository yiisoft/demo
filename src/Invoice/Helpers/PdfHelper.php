<?php
declare(strict_types=1);

namespace App\Invoice\Helpers;

use App\Invoice\Helpers\MpdfHelper;
use App\Invoice\Helpers\CustomValuesHelper as CVH;
use App\Invoice\Setting\SettingRepository as SR;
use Yiisoft\Session\SessionInterface as Session;

Class pdfHelper
{
    private SR $s;
    
    private Session $session;

    public function __construct(SR $s, Session $session) {
        $this->s = $s;
        $this->session = $session;
    }
    
    private function locale_to_language(){
        $dropdown_locale = (string)$this->session->get('_language');
        $session_list = $this->s->locale_language_array();
        return $session_list[$dropdown_locale] ?? null;
    }
        
    // Used on line 104 to determine the cldr 
    private function get_print_language($quote){
        $locale_lang = $this->locale_to_language();
        // Get the client language if set : otherwise use the locale as basis
        $print_language = (!empty($quote->getClient()->getClient_language()) ?  $quote->getClient()->getClient_language() : $locale_lang);              
        $this->session->set('print_language', $print_language); 
        return  $print_language;
    }
    
    /**
     * Generate the PDF for a quote
     *
     * @param $quote_id
     * @param bool $stream
     * @param null $quote_template
     *
     * @return string
     * @throws \Mpdf\MpdfException
     */

    // Used by QuoteController pdf function 
    public function generate_quote_pdf($quote_id, $user_id, $stream, $custom, $quote_amount, $quote_custom_values,$cR, $cvR, $cfR, $qiR, $qiaR, $qR, $qtrR, $uiR,
                                $viewrenderer) 
    {       
        $quote = ($qR->repoQuoteLoadedquery((string)$quote_id) ?? []);
        // If userinv details have been filled, use these details
        $userinv = ($uiR->repoUserInvcount((string)$user_id)>0 ? $uiR->repoUserInvquery((string)$user_id) : null);
        // If a template has been selected in the dropdown use it otherwise use the default 'quote' template under
        // views/invoice/template/quote/pdf/quote.pdf
        $quote_template = (!empty($this->s->setting('pdf_quote_template')) ? $this->s->setting('pdf_quote_template') : 'quote');            
               
        // Determine if discounts should be displayed if there are items on the quote     
        $items = ($qiR->repoCount((string)$quote_id) > 0 ? $qiR->repoQuoteItemIdquery($quote_id) : []);

        $show_item_discounts = false;
        foreach ($items as $item) {
            if ($item->getDiscount_amount() !== '0.00') {
                $show_item_discounts = true;
            }
        }
        
        // Get all data related to building the quote including custom fields
        $data = [
            'quote' => $quote,
            'quote_tax_rates' => (($qtrR->repoCount($this->session->get('quote_id')) > 0) ? $qtrR->repoQuotequery($this->session->get('quote_id')) : null), 
            'items' => $items,
            'qiaR'=>$qiaR,
            'output_type' => 'pdf',
            'show_item_discounts' => $show_item_discounts,
            // Show the custom fields if the user has answered yes on the modal ie $custom = true
            'show_custom_fields' => $custom,
            // Custom fields appearing near the top of the quote
            'custom_fields'=>$cfR->repoTablequery('quote_custom'),
            'custom_values'=>$cvR->attach_hard_coded_custom_field_values_to_custom_field($cfR->repoTablequery('quote_custom')),
            'cvH'=> new CVH($this->s),
            'quote_custom_values' => $quote_custom_values,
            'top_custom_fields' =>$viewrenderer->renderPartialAsString('/invoice/template/quote/pdf/top_custom_fields', [
                'custom_fields'=>$cfR->repoTablequery('quote_custom'),
                'cvR'=>$cvR, 
                'quote_custom_values'=> $quote_custom_values,  
                'cvH'=> new CVH($this->s),
                's'=>$this->s,   
            ]),    
            // Custom fields appearing at the bottom of the quote
            'view_custom_fields'=>$viewrenderer->renderPartialAsString('/invoice/template/quote/pdf/view_custom_fields', [
                'custom_fields'=>$cfR->repoTablequery('quote_custom'),
                'cvR'=>$cvR,
                'quote_custom_values'=> $quote_custom_values,  
                'cvH'=> new CVH($this->s),
                's'=>$this->s,   
            ]),        
            's'=>$this->s,
            'countryhelper'=>new CountryHelper(),
            'userinv'=>$userinv,
            'client'=>$cR->repoClientquery((string)$quote->getClient()->getClient_id()),
            'quote_amount'=>$quote_amount,            
            // Use the temporary print language to define cldr            
            'cldr'=> array_keys($this->s->locale_language_array(), $this->get_print_language($quote, $this->s, $this->session)),
        ];        
        // Quote Template will be either 'quote' or a custom designed quote in the folder.
        $html = $viewrenderer->renderPartialAsString('/invoice/template/quote/pdf/'.$quote_template,$data);
        // Set the print language to null for future use
        $this->session->set('print_language','');
        $mpdfhelper = new MpdfHelper(); 
        $filename = $this->s->trans('quote') . '_' . str_replace(['\\', '/'], '_', $quote->getNumber());
        return $mpdfhelper->pdf_create($html, $filename, $stream, $quote->getPassword(), $this->s, $isInvoice = false, $quote);
    }   //generate_quote_pdf
    
    // Used by InvController pdf function 
    public function generate_inv_pdf($inv_id, $user_id, $stream, $custom, $inv_amount, $inv_custom_values,$cR, $cvR, $cfR, $iiR, $iiaR, $iR, $itrR, $uiR,
                                $viewrenderer) 
    {       
        $inv = ($iR->repoInvLoadedquery((string)$inv_id) ?? []);
        // If userinv details have been filled, use these details
        $userinv = ($uiR->repoUserInvcount((string)$user_id)>0 ? $uiR->repoUserInvquery((string)$user_id) : null);
        // If a template has been selected in the dropdown use it otherwise use the default 'inv' template under
        // views/invoice/template/inv/pdf/invoice.pdf
        $inv_template = (!empty($this->s->setting('pdf_invoice_template')) ? $this->s->setting('pdf_invoice_template') : 'invoice');            
               
        // Determine if discounts should be displayed if there are items on the invoice      
        $items = ($iiR->repoCount((string)$inv_id) > 0 ? $iiR->repoInvItemIdquery($inv_id) : []);

        $show_item_discounts = false;
        foreach ($items as $item) {
            if ($item->getDiscount_amount() !== '0.00') {
                $show_item_discounts = true;
            }
        }
        
        // Get all data related to building the inv including custom fields
        $data = [
            'inv' => $inv,
            'inv_tax_rates' => (($itrR->repoCount($this->session->get('inv_id')) > 0) ? $itrR->repoInvquery($this->session->get('inv_id')) : null), 
            'items' => $items,
            'iiaR'=>$iiaR,
            'output_type' => 'pdf',
            'show_item_discounts' => $show_item_discounts,
            // Show the custom fields if the user has answered yes on the modal ie $custom = true
            'show_custom_fields' => $custom,
            // Custom fields appearing near the top of the quote
            'custom_fields'=>$cfR->repoTablequery('inv_custom'),
            'custom_values'=>$cvR->attach_hard_coded_custom_field_values_to_custom_field($cfR->repoTablequery('inv_custom')),
            'cvH'=> new CVH($this->s),
            'inv_custom_values' => $inv_custom_values,
            'top_custom_fields' =>$viewrenderer->renderPartialAsString('/invoice/template/invoice/pdf/top_custom_fields', [
                'custom_fields'=>$cfR->repoTablequery('inv_custom'),
                'cvR'=>$cvR, 
                'inv_custom_values'=> $inv_custom_values,  
                'cvH'=> new CVH($this->s),
                's'=>$this->s,   
            ]),    
            // Custom fields appearing at the bottom of the quote
            'view_custom_fields'=>$viewrenderer->renderPartialAsString('/invoice/template/invoice/pdf/view_custom_fields', [
                'custom_fields'=>$cfR->repoTablequery('inv_custom'),
                'cvR'=>$cvR,
                'inv_custom_values'=> $inv_custom_values,  
                'cvH'=> new CVH($this->s),
                's'=>$this->s,   
            ]),        
            's'=>$this->s,
            'countryhelper'=>new CountryHelper(),
            'userinv'=>$userinv,
            'client'=>$cR->repoClientquery((string)$inv->getClient()->getClient_id()),
            'inv_amount'=>$inv_amount,            
            // Use the temporary print language to define cldr            
            'cldr'=> array_keys($this->s->locale_language_array(), $this->get_print_language($inv, $this->s, $this->session)),
        ];        
        // Inv Template will be either 'inv' or a custom designed inv in the folder.
        $html = $viewrenderer->renderPartialAsString('/invoice/template/invoice/pdf/'.$inv_template,$data);
        // Set the print language to null for future use
        $this->session->set('print_language','');
        $mpdfhelper = new MpdfHelper(); 
        $filename = $this->s->trans('invoice') . '_' . str_replace(['\\', '/'], '_', $inv->getNumber());
        //$isInvoice is assigned to true
        return $mpdfhelper->pdf_create($html, $filename, $stream, $inv->getPassword(), $this->s, true, $inv);
    }   //generate_quote_pdf
    
    public function generate_inv_sumex() {}
} 