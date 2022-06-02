<?php

declare(strict_types=1);

namespace App\Invoice\Setting;

use App\Invoice\Entity\Setting;
use App\Invoice\Inv\InvRepository as IR;
use Cycle\ORM\Select;
use Throwable;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Files\FileHelper;
use Yiisoft\Files\PathMatcher\PathMatcher;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Yii\Cycle\Data\Reader\EntityReader;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;
use App\Invoice\Libraries\Lang;

final class SettingRepository extends Select\Repository
{
    private EntityWriter $entityWriter;
    
    public $settings = [];
    
    private $session;

    public function __construct(Select $select, EntityWriter $entityWriter, SessionInterface $session)
    {
        $this->entityWriter = $entityWriter;
        $this->session = $session;
        parent::__construct($select);
    }
    
    /**
     * Get settings without filter
     *
     * @psalm-return DataReaderInterface<int, Setting>
     */
    public function findAllPreloaded(): DataReaderInterface
    {
        $query = $this->select();
        return $this->prepareDataReader($query);
    }
    
    public function repoCount(string $setting_key) : int {
        $count = $this->select()
                      ->where(['setting_key' => $setting_key])                                
                      ->count();
        return $count; 
    }
            
    /**
     * @throws Throwable
     */
    public function save(Setting $setting): void
    {
        if ($setting->getSetting_key() === 'default_language') {$this->session->set('_language',$setting->getSetting_value());}
        $this->entityWriter->write([$setting]);        
    }
    
    /**
     * @throws Throwable
     */
    public function delete(Setting $setting): void
    {
        $this->entityWriter->delete([$setting]);
    }

    private function prepareDataReader($query): EntityReader
    {
        return (new EntityReader($query))->withSort(
            Sort::only(['id', 'setting_key', 'setting_value'])
                ->withOrder(['setting_key' => 'asc'])
        );
    }
    
    public function repoSettingquery(string $setting_id): Setting
    {
        $query = $this
            ->select()
            ->where(['id' => $setting_id]);
        return  $query->fetchOne();        
    }
    
    public function withKey(string $setting_key): ?Setting
    {
        $query = $this
            ->select()
            ->where(['setting_key' => $setting_key]);
        return  $query->fetchOne();
    }
    
    public function withValue(string $setting_value): ?Setting
    {
        $query = $this
            ->select()
            ->where(['setting_value' => $setting_value]);
        return  $query->fetchOne();
    }
    
    public function expand(string $setting_key, string $setting_value): ?Setting
    {
        $one_setting = $this->withKey($setting_key);
        if (!empty($one_setting)) {
              $one_setting->setting_value = $setting_value;
              $this->save($one_setting);        
        } else {
            $newsetting = new Setting();
            $newsetting->setting_key = $setting_key;
            $newsetting->setting_value = $setting_value;
            $this->save($newsetting);        
        }       
    }
    
    public function getValue(string $setting_key)
    {
        $one_setting = $this->withKey($setting_key);
        if (!empty($one_setting) && !empty($one_setting->setting_value)) {
            $g = $one_setting->setting_value;
            return $g;
        } else {
            return '';
            
        }        
    }
    
    public function load_setting($setting_key)
    {
        $setting = $this->select()->where(['setting_key' => $setting_key]);
        foreach ($setting as $data) {
            $this->settings[$data->setting_key] = $data->setting_value;
        }
        return $this->settings['default_language'];              
    }
    
    public function load_settings()
    {
        $all_settings = $this->findAllPreloaded();  
        foreach ($all_settings as $data) {
            $this->settings[$data->getSetting_key()] = $data->getSetting_value();
        }        
    }
    
    public function get_setting($key, $default = '')
    {
        $this->load_settings();
        return (isset($this->settings[$key])) ? $this->settings[$key] : $default;
    }
    
    public function setting($key, $default = '')
    {
        $this->load_settings();
        return (isset($this->settings[$key])) ? $this->settings[$key] : $default;
    }    
    
    public function set_setting($key, $value)
    {
        $this->settings[$key] = $value;
    }
    
    public function expandDirectoriesMatrix($base_dir, $level = 0) {
        $directories = [];
        foreach(scandir($base_dir) as $file) {
            if($file == '.' || $file == '..') continue;
            $dir = $base_dir.DIRECTORY_SEPARATOR.$file;
            if(is_dir($dir)) {
                $directories[]= array(
                        'level' => $level,
                        'name' => $file,
                        'path' => $dir,
                        'children' => $this->expandDirectoriesMatrix($dir, $level +1)
                );
            }
        }
        return $directories;
    }
    //$s->check_select(Html::encode($body['family_id'] ?? ''), $family->id) 
    public function check_select($value1, $value2 = null, $operator = '==', $checked = false)
    {
    $select = $checked ? 'checked="checked"' : 'selected="selected"';

    // Instant-validate if $value1 is a bool value
    if (is_bool($value1) && $value2 === null) {
        echo $value1 ? $select : '';
        return;
    }

    switch ($operator) {
        case '==':
            $echo_selected = $value1 == $value2 ? true : false;
            break;
        case '!=':
            $echo_selected = $value1 != $value2 ? true : false;
            break;
        case 'e':
            $echo_selected = empty($value1) ? true : false;
            break;
        case '!e':
            $echo_selected = empty($value1) ? true : false;
            break;
        default:
            $echo_selected = $value1 ? true : false;
            break;
    }

    echo $echo_selected ? $select : '';
    }
    
    public function locale_language_array() : array {
        $language_list = [
            'ar'=>'Arabic',
            'en'=>'English',            
            'ja'=>'Japanese',
            'nl'=>'Dutch',
            // There is currently no russian language folder under invoiceplane. Substitute English here with Russian when it becomes available
            'ru'=>'English',
            // Use camelcase here => remove the space between Chinese and Simplified and in the original folder otherwise it will not be 
            // retrieved
            'zh'=>'ChineseSimplified',    
            
        ];
        return $language_list;
    }
    
    /**
     * @return array
     */
    public function getStatuses()
    {
        return [
            1 => [
                'label' => $this->trans('draft'),
                'class' => 'draft',
                'href' => 1
            ],
            2 => [
                'label' => $this->trans('sent'),
                'class' => 'sent',
                'href' => 2
            ],
            3 => [
                'label' => $this->trans('viewed'),
                'class' => 'viewed',
                'href' => 3
            ],
            4 => [
                'label' => $this->trans('approved'),
                'class' => 'approved',
                'href' => 4
            ],
            5 => [
                'label' => $this->trans('rejected'),
                'class' => 'rejected',
                'href' => 5
            ],
            6 => [
                'label' => $this->trans('canceled'),
                'class' => 'canceled',
                'href' => 6
            ]
        ];       
    }
    
    // The default_language setting is a desperate setting and should be set infrequently 
    // If the locale is set, and it exists in the above language array, then use it in preference to the default_language
    public function get_folder_language(){
        // Prioritise the use of the locale dropdown since it will always be set. config/params/locales
        $sess_lang = $this->session->get('_language');
        // The print language is set under the get_print_language function in pdfHelper which uses the clients language as priority
        $print_lang = $this->session->get('print_language');
        // Use the print language if it is not empty over the locale language
        if (empty($print_lang)) {
            return (!empty($sess_lang) && (array_key_exists($sess_lang, $this->locale_language_array()))) 
                             ? $this->locale_language_array()[$sess_lang] 
            : $this->setting('default_language');         
        }
        if (!empty($print_lang)) {
            return $print_lang;
        }
    }
    
    public function load_language_folder()
    {   
        $folder_language = $this->get_folder_language();           
        $lang = new Lang();
        $lang->load('ip',$folder_language);
        $lang->load('gateway',$folder_language);
        $lang->load('custom',$folder_language);
        $lang->load('merchant',$folder_language);
        $lang->load('form_validation',$folder_language);
        $languages = $lang->_language;
        return $languages;
    }  
    
    public function trans($words)
    {
        foreach ($this->load_language_folder() as $key => $value){
             if ($words === $key){
                  return $value;                                    
             }
        }                   
    }
    
    /**
    * Lang
    *
    * Fetches a language variable and optionally outputs a form label
    *
    * @param	string	$line		The language line
    * @param	string	$for		The "for" value (id of the form element)
    * @param	array	$attributes	Any additional HTML attributes
    * @return	string
    */
   public function lang($in_line = '', $for = '', $attributes = array())
   {
           $line = $this->trans($in_line);

           if ($for !== '')
           {
                   $line = '<label for="'.$for.'"'._stringify_attributes($attributes).'>'.$line.'</label>';
           }

           return $line;
    }
    
    public function random_string($type = 'alnum', $len = 8)
    {
                    switch ($type)
                    {
                            case 'basic':
                                    return mt_rand();
                            case 'alnum':
                            case 'numeric':
                            case 'nozero':
                            case 'alpha':
                                    switch ($type)
                                    {
                                            case 'alpha':
                                                    $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                                                    break;
                                            case 'alnum':
                                                    $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                                                    break;
                                            case 'numeric':
                                                    $pool = '0123456789';
                                                    break;
                                            case 'nozero':
                                                    $pool = '123456789';
                                                    break;
                                    }
                                    return substr(str_shuffle(str_repeat($pool, (int)ceil($len / strlen($pool)))), 0, $len);
                            case 'unique': // todo: remove in 3.1+
                            case 'md5':
                                    return md5(uniqid(mt_rand()));
                            case 'encrypt': // todo: remove in 3.1+
                            case 'sha1':
                                    return sha1(uniqid(mt_rand(), TRUE));
                    }
    }
    
    public function invoice_mark_viewed($invoice_id, IR $iR)
    {
        $invoice = $iR->repoInvUnloadedquery($invoice_id);
        
        //mark as viewed if status is 2                                    
        if (($iR->repoCount($invoice_id)>0) && $invoice->getStatus_id()===2){
            //set the invoice to viewed status ie 3
            $invoice->setStatus_id(3);
            $iR->save($invoice);
        }
        
        //set the invoice to 'read only' only once it has been viewed according to 'Other settings' 
        //2 sent, 3 viewed, 4 paid,
        if ($this->setting('read_only_toggle') == 3)
        {
            $invoice = $iR->repoInvUnloadedquery($invoice_id);
            $invoice->setIs_read_only(true);
            $iR->save($invoice);
        }
    }
    
    public function quote_mark_viewed($quote_id, QR $qR) : void
    {
        $quote = $qR->repoQuoteStatusquery($quote_id,2);
        
        //mark as viewed if status is 2
        if ($qR->repoCount($quote_id)>0){
            //set the quote to viewed status ie 3
            $quote->setStatus_id(3);
            $qR->save($quote);
        }
        
        //set the quote to 'read only' only once it has been viewed according to 'Other settings' 
        //2 sent, 3 viewed, 4 paid,
        if ($this->setting('read_only_toggle') == 3)
        {
            $quote = $qR->repoQuoteUnloadedquery($quote_id);
            $quote->setIs_read_only(true);
            $qR->save($quote);
        }
    }
    
    public function invoice_mark_sent($invoice_id, IR $iR) : void
    {
        $invoice = $iR->repoInvUnloadedquery($invoice_id);
        //draft->sent->view->paid
        //set the invoice to sent ie. 2                                    
        if (!empty($invoice) && $invoice->getStatus_id() === 1){
            $invoice->setStatus_id(2);
        }
        //set the invoice to read only ie. not updateable, if invoice_status_id is 2
        if ($this->withKey('read_only_toggle')->getSetting_value() === 2)
        {
            $invoice->setIs_read_only(1);            
        }
        $iR->save($invoice);
    }
    
    public function quote_mark_sent($quote_id, QR $qR) : void
    {
        $quote = $qR->repoQuoteStatusquery($quote_id,1);
        //draft->sent->view->paid
        //set the quote to sent ie. 2                                    
        if (!empty($quote)){
            $quote->setStatus_id(2);
            $qR->save($quote);
        }
        
        //set the quote to read only ie. not updateable, if quote_status_id is 2
        if ($this->setting('read_only_toggle') == 2)
        {
            $quote = $qR->repoQuoteUnloadedquery($quote_id);
            $quote->setIs_read_only(1);
            $qR->save($quote);
        }
    }
    
    
    // Add to src/Invoice
    public static function getPlaceholderRelativeUrl()
    {
        return '/Uploads/';
    } 
    
    public static function getAssetholderRelativeUrl()
    {        
        return '/Asset/';
    }
    
    public static function getCustomerfolderRelativeUrl()
    {        
        return '/Customer_files/';
    }
    
    // Append to uploads folder
    public static function getTempMpdffolderRelativeUrl()
    {        
        return '/Temp/Mpdf/';
    }
    
    
    public static function getTemplateholderRelativeUrl()
    {
        return '/Invoice_templates/Pdf/';
    }        
    
    // Append to uploads folder
    public static function getUploadsArchiveholderRelativeUrl()
    {
        return '/Archive';
    }
    
    public function format_currency($amount)
    {
        $this->load_settings();
        $currency_symbol =$this->setting('currency_symbol');
        $currency_symbol_placement = $this->setting('currency_symbol_placement');
        $thousands_separator = $this->setting('thousands_separator');
        $decimal_point = $this->setting('decimal_point');

        if ($currency_symbol_placement == 'before') {
            return $currency_symbol . number_format((float)$amount, ($decimal_point) ? 2 : 0, $decimal_point, $thousands_separator);
        } elseif ($currency_symbol_placement == 'afterspace') {
            return number_format((float)$amount, ($decimal_point) ? 2 : 0, $decimal_point, $thousands_separator) . '&nbsp;' . $currency_symbol;
        } else {
            return number_format((float)$amount, ($decimal_point) ? 2 : 0, $decimal_point, $thousands_separator) . $currency_symbol;
        }
    }
    
    //show the decimal point representation character whether a comma, a dot, or something else with maximum of 2 decimal points after the point
    public function format_amount($amount = null)
    {
        $this->load_settings();    
        if ($amount) {
            $thousands_separator = $this->setting('thousands_separator');
            $decimal_point = $this->setting('decimal_point');
            //force the rounding of amounts to 2 decimal points if the decimal point setting is filled.
            return number_format((float)$amount, ($decimal_point) ? 2 : 0, $decimal_point, $thousands_separator);
        }
        return null;
    }

    public function standardize_amount($amount)
    {
        $this->load_settings();
        $thousands_separator = $this->setting('thousands_separator');
        $decimal_point = $this->setting('decimal_point');
        $amt = str_replace($thousands_separator, '', $amount);
        $final_amt = str_replace($decimal_point, '.', $amt);
        return $final_amt;
    }
    
    public function get_invoice_templates($type = 'pdf')
    {
        $aliases = new Aliases(['@base' => dirname(dirname(dirname(__DIR__))), 
                                '@pdf' => '@base/views/invoice/template/invoice/pdf',
                                '@public' =>'@base/views/invoice/template/invoice/public'
                               ]);
        if ($type == 'pdf') {
            $templates = ArrayHelper::map($this->expandDirectoriesMatrix($aliases->get('@pdf'), 0),'name','name');
        } elseif ($type == 'public') {
            $templates = ArrayHelper::map($this->expandDirectoriesMatrix($aliases->get('@public'), 0),'name','name');
        }
        $templates = $this->remove_extension($templates);
        return $templates;
    }
    
    public function get_invoice_archived_folder_aliases() {
        $aliases = new Aliases(['@base' => dirname(dirname(dirname(__DIR__))), 
                                '@archive_invoice' => '@base/src/Invoice/Uploads'.$this->getUploadsArchiveholderRelativeUrl().'/Invoice/'
        ]);
        return $aliases;
    }
    
    public function get_invoice_archived_files_with_filter($invoice_number)
    {        
        $aliases = $this->get_invoice_archived_folder_aliases();
        $filehelper = new FileHelper();
        // TODO Use PathPattern to create *.pdf and '*_'.$invoice_number.'.pdf' pattern
        $filter = (null==$invoice_number ? (new PathMatcher())->doNotCheckFilesystem() : (new PathMatcher())->doNotCheckFilesystem());        
        $files = $filehelper::findFiles($aliases->get('@archive_invoice'), ['recursive'=>false,'filter'=>$filter]);                
        return $files;
    }

    private function remove_extension($files)
    {
        foreach ($files as $key => $file) {
            $files[$key] = str_replace('.php', '', $file);
        }

        return $files;
    }

    public function get_quote_templates($type = 'pdf')
    {
         $aliases = new Aliases(['@base' => dirname(dirname(dirname(__DIR__))), 
                                '@pdf' => '@base/views/invoice/template/quote/pdf',
                                '@public' =>'@base/views/invoice/template/quote/public'
                               ]);
        if ($type == 'pdf') {
            $templates = ArrayHelper::map($this->expandDirectoriesMatrix($aliases->get('@pdf'), 0),'name','name');
        } elseif ($type == 'public') {
            $templates = ArrayHelper::map($this->expandDirectoriesMatrix($aliases->get('@public'), 0),'name','name');
        }
        $templates = $this->remove_extension($templates);
        return $templates;
    }
    
    // php 8.0 compatible gateways for omnipay 3.2
    public function payment_gateways() : array 
    {
        $payment_gateways = array(
            'AuthorizeNet_AIM' => array(
                'apiLoginId' => array(
                    'type' => 'text',
                    'label' => 'Api Login Id',
                ),
                'transactionKey' => array(
                    'type' => 'text',
                    'label' => 'Transaction Key',
                ),
                'testMode' => array(
                    'type' => 'checkbox',
                    'label' => 'Test Mode',
                ),
                'developerMode' => array(
                    'type' => 'checkbox',
                    'label' => 'Developer Mode',
                ),
                //'liveEndpoint' => array(
                //    'type' => 'text',
                //    'label' => 'Live Endpoint',
                //),
                //'developerEndpoint' => array(
                //    'type' => 'text',
                //    'label' => 'Developer Endpoint',
                //),
            ),
            'AuthorizeNet_SIM' => array(
                'apiLoginId' => array(
                    'type' => 'text',
                    'label' => 'Api Login Id',
                ),
                'transactionKey' => array(
                    'type' => 'text',
                    'label' => 'Transaction Key',
                ),
                'testMode' => array(
                    'type' => 'checkbox',
                    'label' => 'Test Mode',
                ),
                'developerMode' => array(
                    'type' => 'checkbox',
                    'label' => 'Developer Mode',
                ),
                //'liveEndpoint' => array(
                //    'type' => 'text',
                //    'label' => 'Live Endpoint',
                //),
                //'developerEndpoint' => array(
                //    'type' => 'text',
                //    'label' => 'Developer Endpoint',
                //),
                //'hashSecret' => array(
                //    'type' => 'text',
                //    'label' => 'Hash Secret',
                //),
            ),
            'PayPal_Express' => array(
                'username' => array(
                    'type' => 'text',
                    'label' => 'Username',
                ),
                'password' => array(
                    'type' => 'password',
                    'label' => 'Password',
                ),
                'signature' => array(
                    'type' => 'password',
                    'label' => 'Signature',
                ),
                'testMode' => array(
                    'type' => 'checkbox',
                    'label' => 'Test Mode',
                ),
                //'solutionType' => array(
                //    'type' => 'text',
                //    'label' => 'Solution Type',
                //),
                //'landingPage' => array(
                //    'type' => 'text',
                //    'label' => 'Landing Page',
                //),
                //'brandName' => array(
                //    'type' => 'text',
                //    'label' => 'Brand Name',
                //),
                //'headerImageUrl' => array(
                //    'type' => 'text',
                //    'label' => 'Header Image Url',
                //),
                //'logoImageUrl' => array(
                //    'type' => 'text',
                //    'label' => 'Logo Image Url',
                //),
                //'borderColor' => array(
                //    'type' => 'text',
                //    'label' => 'Border Color',
                //),
            ),
            'PayPal_Pro' => array(
                'username' => array(
                    'type' => 'text',
                    'label' => 'Username',
                ),
                'password' => array(
                    'type' => 'password',
                    'label' => 'Password',
                ),
                'signature' => array(
                    'type' => 'text',
                    'label' => 'Signature',
                ),
                'testMode' => array(
                    'type' => 'checkbox',
                    'label' => 'Test Mode',
                ),
            ),
            'Stripe' => array(
                'apiKey' => array(
                    'type' => 'password',
                    'label' => 'Api Key',
                ),
            ),
        );
        return $payment_gateways;
    }
    
    public function number_formats() : array {
           /*
            | -------------------------------------------------------------------
            | Number formats
            | -------------------------------------------------------------------
            | This is a list of available number formats that are used by
            | the settings:
            |
            | US/UK format...................... 1,000,000.00
            | European format................... 1.000.000,00
            | ISO 80000-1 with decimal point.... 1 000 000.00
            | ISO 80000-1 with decimal comma.... 1 000 000,00
            | Compact with decimal point........   1000000.00
            | Compact with decimal comma........   1000000,00
            |
            */

            $number_formats = [
            'number_format_us_uk' =>
                [
                    'label' => 'number_format_us_uk',
                    'decimal_point' => '.',
                    'thousands_separator' => ',',
                ],
            'number_format_european' =>
                [
                    'label' => 'number_format_european',
                    'decimal_point' => ',',
                    'thousands_separator' => '.',
                ],
            'number_format_iso80k1_point' =>
                [
                    'label' => 'number_format_iso80k1_point',
                    'decimal_point' => '.',
                    'thousands_separator' => ' ',
                ],
            'number_format_iso80k1_comma' =>
                [
                    'label' => 'number_format_iso80k1_comma',
                    'decimal_point' => ',',
                    'thousands_separator' => ' ',
                ],
            'number_format_compact_point' =>
                [
                    'label' => 'number_format_compact_point',
                    'decimal_point' => '.',
                    'thousands_separator' => '',
                ],
            'number_format_compact_comma' =>
                [
                    'label' => 'number_format_compact_comma',
                    'decimal_point' => ',',
                    'thousands_separator' => '',
                ],
            ];
            return $number_formats;
    }
}
