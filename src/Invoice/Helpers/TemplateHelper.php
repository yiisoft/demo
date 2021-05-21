<?php
namespace frontend\modules\invoice\application\helpers;

use frontend\modules\invoice\application\models\ci\Mdl_settings;
use frontend\modules\invoice\application\components\Utilities;
use frontend\modules\invoice\application\helpers\NumberHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\db\Expression;
use Yii;

Class TemplateHelper {
    
    public static function getSettings()
    {
        $mdl_settings = new Mdl_settings();
        $mdl_settings->load_settings();
        return $mdl_settings;
    }
    
    //the email template body is filled with data from the invoice and the invoice's relations
    public static function parse_template($object, $body)
    {
        if (preg_match_all('/{{{([^{|}]*)}}}/', $body, $template_vars)) {
            foreach ($template_vars[1] as $var) {
                switch ($var) {
                    case 'name':
                        $replace = Html::encode($object->customerdetails->name);
                        break;
                    case 'surname':
                        $replace = Html::encode($object->customerdetails->surname);
                        break;
                    case 'contactmobile':
                        $replace = $object->customerdetails->contactmobile;
                        break;
                    case 'email':
                        $replace = $object->customerdetails->email;
                        break;
                    case 'invoice_id':
                        $replace = $object->invoice_id;
                        break;
                    case 'invoice_status_id':
                        $replace = $object->status->email;
                        break;
                    case 'invoice_date_due':
                        $replace = Yii::$app->formatter->asDate($object->invoice_date_due,'php:d mm Y');
                        break;
                    case 'invoice_date_created':
                        $replace = Yii::$app->formatter->asDate($object->invoice_date_created,'php:d mm Y');
                        break;
                    case 'invoice_total':
                        $replace = NumberHelper::format_currency($object->salesinvoiceamount->invoice_total);
                        break;
                    case 'invoice_paid':
                        $replace = NumberHelper::format_currency($object->salesinvoiceamount->invoice_paid);
                        break;
                    case 'invoice_balance':
                        $replace = NumberHelper::format_currency($object->salesinvoiceamount->invoice_balance);
                        break;
                     case 'invoice_terms':
                        $replace = NumberHelper::format_currency($object->invoice_terms);
                        break;
                    case 'invoice_guest_url':
                        $replace = Url::to(['view/invoice/','invoice_url_key'=> $object->invoice_url_key],true);
                        break;
                    case 'payment_method':
                        $replace = $object->paymentmethod->payment_method_name;
                        break;
                    default:
                        $replace = isset($object->{$var}) ? $object->{$var} : $var;                      
                }
                $body = str_replace('{{{' . $var . '}}}', $replace, $body);
            }
        }
        return $body;
    }

    public static function select_pdf_invoice_template($invoice)
    {
        $now = new Expression('NOW()');
        if (($invoice->invoice_date_due < $now) && ($invoice->salesinvoiceamount->invoice_balance <> 0.00) && ($invoice->salesinvoiceamount->invoice_paid < $invoice->salesinvoiceamount->invoice_total)) {
            // Use the overdue template
            $array = [
                       'setting' => TemplateHelper::getSettings()->setting('pdf_invoice_template_overdue'),
                       'watermark' => Utilities::getAssetholderRelativeUrl().'core/img/overdue.png' 
                     ];
            return $array;
        } elseif ($invoice->invoice_status_id == 4) {
            // Use the paid template
            $array = [
                       'setting' => TemplateHelper::getSettings()->setting('pdf_invoice_template_paid'),
                       'watermark' => Utilities::getAssetholderRelativeUrl().'core/img/paid.png' 
                     ];
            return $array;
        } else {
            // Use the default template
            $array = [
                       'setting' => TemplateHelper::getSettings()->setting('pdf_invoice_template'),
                       'watermark' => null
                     ];
            return $array;
        }
    }

    public static function select_email_invoice_template($invoice)
    {
        $now = new Expression('NOW()');
        if ($invoice->invoice_date_due < $now) {
            // Use the overdue template
            return TemplateHelper::getSettings()->setting('email_invoice_template_overdue');
        } elseif ($invoice->invoice_status_id == 4) {
            // Use the paid template
            return TemplateHelper::getSettings()->setting('email_invoice_template_paid');
        } else {
            // Use the default template
            return TemplateHelper::getSettings()->setting('email_invoice_template');
        }
    }
}