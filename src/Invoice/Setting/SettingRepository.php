<?php

declare(strict_types=1);

namespace App\Invoice\Setting;

use App\Invoice\Entity\Setting;
use Cycle\ORM\Select;
use Throwable;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Cycle\Data\Reader\EntityReader;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;
use App\Invoice\Libraries\Lang;

final class SettingRepository extends Select\Repository
{
    private EntityWriter $entityWriter;
    
    public $settings = [];

    public function __construct(Select $select, EntityWriter $entityWriter)
    {
        $this->entityWriter = $entityWriter;
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
            
    /**
     * @throws Throwable
     */
    public function save(Setting $setting): void
    {
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
            $newsetting->setting_key = $key;
            $newsetting->setting_value = $value;
            $this->save($newsetting);        
        }       
    }
    
    public function getValue(string $setting_key)
    {
        $one_setting = $this->withKey($setting_key);
        if (!empty($one_setting) && !empty($one_setting->setting_value)) {
            $g = $one_setting->setting_value;
            return $g;
        }
        else return '';        
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
            $this->settings[$data->setting_key] = $data->setting_value;
        }        
    }
    
    public function get_setting($key, $default = '')
    {
        return (isset($this->settings[$key])) ? $this->settings[$key] : $default;
    }
    
    public function setting($key, $default = '')
    {
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
    
    public function dictionary()
    {
        $language = $this->load_setting('default_language');
        $lang = [];
        $lang = new Lang();
        $lang->load('ip', $language);
        $lang->load('gateway', $language);
        $lang->load('custom',$language);
        $lang->load('merchant',$language);
        $lang->load('form_validation',$language);
        $languages = $lang->_language;
        return $languages;
    }  
    
    public function trans($quote)
    {
        foreach ($this->dictionary() as $key => $value){
             if ($quote === $key){
                  return $value;                                    
             }
        }                   
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
    
    public function mark_viewed($invoice_id)
    {
        $invoice = Salesinvoice::find()
                   ->where(['=','invoice_id',$invoice_id])->andWhere(['invoice_status_id' => 2])
                   ->one();
        
        //mark as viewed if status is 2                                    
        if (!empty($invoice)){
            //set the invoice to viewed status ie 3
            $invoice->invoice_status_id = 3;
            $invoice->save();
        }
        
        //$mdl_settings = new Mdl_settings();
        
        //set the invoice to 'read only' only once it has been viewed according to 'Other settings' 
        //2 sent, 3 viewed, 4 paid,
        if ($this->mdl_settings->setting('read_only_toggle') == 3)
        {
            $invoice = Salesinvoice::find()
                   ->where(['=','invoice_id',$invoice_id])
                   ->one();
            $invoice->is_read_only = 1;
            $invoice->save();
        }
    }
    
    public static function mark_sent($invoice_id)
    {
        $invoice = Salesinvoice::find()
                   ->where(['=','invoice_id',$invoice_id])
                   ->andWhere(['=','invoice_status_id', 1])
                   ->one();
        //draft->sent->view->paid
        //set the invoice to sent ie. 2                                    
        if (!empty($invoice)){
            $invoice->invoice_status_id = 2;
            $invoice->save();
        }
        
        //$mdl_settings = new Mdl_settings();
        
        //set the invoice to read only ie. not updateable, if invoice_status_id is 2
        if ($this->mdl_settings->setting('read_only_toggle') == 2)
        {
            $invoice = Salesinvoice::find()
                   ->where(['=','invoice_id',$invoice_id])
                   ->one();
            $invoice->is_read_only = 1;
            $invoice->save();
        }
    }
    
    public static function getPlaceholderRelativeUrl()
    {
        return '/Invoice/Uploads/';
    } 
    
    public static function getPlaceholderAbsoluteUrl(){
        return Url::to($this->getPlaceholderRelativeUrl(),true);                                    
    }
    
    public static function getAssetholderRelativeUrl()
    {        
        return '/Invoice/Asset/';
    }
    
    public static function getCustomerfolderRelativeUrl()
    {        
        return '/Invoice/Uploads/Customer_files/';
    }
    
    public static function getMpdfTempfolderRelativeUrl()
    {        
        return '/Invoice/Uploads/Temp/Mpdf/';
    }
    
    public static function getTemplateholderRelativeUrl()
    {
        return '/Invoice_templates/Pdf/';
    }        
    
    public static function getUploadsArchiveholderRelativeUrl()
    {
        return '/Invoice/Uploads/Archive';
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
        $amount = str_replace($thousands_separator, '', $amount);
        $amount = str_replace($decimal_point, '.', $amount);
        return $amount;
    }
}
