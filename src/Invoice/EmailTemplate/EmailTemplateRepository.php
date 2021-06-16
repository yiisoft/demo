<?php

declare(strict_types=1);

namespace App\Invoice\EmailTemplate;

use App\Invoice\Entity\EmailTemplate;
use App\Invoice\Entity\Setting\SettingRepository;
use Cycle\ORM\Select;
use Throwable;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Cycle\Data\Reader\EntityReader;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Files\FileHelper;

final class EmailTemplateRepository extends Select\Repository
{
    private EntityWriter $entityWriter;

    public function __construct(Select $select, EntityWriter $entityWriter)
    {
        $this->entityWriter = $entityWriter;
        parent::__construct($select);
    }

    /**
     * @psalm-return DataReaderInterface<int, EmailTemplate>
     */
    public function findAllPreloaded(): DataReaderInterface
    {
        $query = $this->select();
        return $this->prepareDataReader($query);
    }

    /**
     * @throws Throwable
     */
    public function save(EmailTemplate $emailtemplate): void
    {
        $this->entityWriter->write([$emailtemplate]);
    }
    
    /**
     * @throws Throwable
     */
    public function delete(EmailTemplate $emailtemplate): void
    {
        $this->entityWriter->delete([$emailtemplate]);
    }

    private function prepareDataReader($query): EntityReader
    {
        return (new EntityReader($query))->withSort(
            Sort::only(['id', 'email_template_title', 'email_template_from_name', 'email_template_from_email'])
                ->withOrder(['id' => 'desc'])
        );
    }
    
    public function repoEmailTemplatequery(string $email_template_id): EmailTemplate
    {
        $query = $this
            ->select()
            ->where(['id' => $email_template_id]);
        return  $query->fetchOne();        
    }
    
    public static function getSettings(SettingRepository $setting)
    {
        $setting->load_settings();
        return $setting;
    }
    
    public function get_invoice_templates($type = 'pdf')
    {
        $pdf_template_directory = dirname(dirname(dirname(__DIR__))).'/views/invoice/template/template_invoice/pdf'; 
        $public_template_directory = dirname(dirname(dirname(__DIR__))).'/views/invoice/template/template_invoice/public';
        if ($type == 'pdf') {
              $templates = FileHelper::findFiles($pdf_template_directory, [
                                        'only' => ['*.php'],
                                        'recursive' => false,
                                ]);
        } elseif ($type == 'public') {
              $templates = FileHelper::findFiles($public_template_directory, [
                                        'only' => ['*.php'],
                                        'recursive' => false,
                                ]);
        }
        $templates = $this->remove_extension($templates);
        $templates = $this->remove_path($templates);
        return $templates;
    }
    
    public function get_quote_templates($type = 'pdf')
    {
        $pdf_template_directory = dirname(dirname(dirname(__DIR__))).'/views/invoice/template/template_quote/pdf'; 
        $public_template_directory = dirname(dirname(dirname(__DIR__))).'/views/invoice/template/template_quote/public';
        if ($type == 'pdf') {
            $templates = FileHelper::findFiles($pdf_template_directory, [
                                        'only' => ['*.pdf'],
                                        'recursive' => false,
                                ]);
        } elseif ($type == 'public') {
            $templates = FileHelper::findFiles($public_template_directory, [
                                        'only' => ['*.pdf'],
                                        'recursive' => false,
                                ]);
        }
        $templates = $this->remove_extension($templates);
        $templates = $this->remove_path($templates);
        return $templates;
    }

    private function remove_extension($files)
    {
        foreach ($files as $key => $file) {
            $files[$key] = str_replace('.php', '', $file);
        }
        return $files;
    }
    
    private function remove_path($files)
    {
        //https://stackoverflow.com/questions/1418193/how-do-i-get-a-file-name-from-a-full-path-with-php
        foreach ($files as $key => $file) {
            $files[$key] = basename($file);
        }
        return $files;
    }
            
    private function flat_an_array($a)
    {
        foreach($a as $i)
        {
            if(is_array($i)) 
            {
                if($na) $na = array_merge($na,flat_an_array($i));
                else $na = flat_an_array($i);
            }
            else $na[] = $i;
        }
        return $na;
    }
    
    public static function select_pdf_invoice_template($invoice, SettingRepository $setting)
    {
        $now = new Expression('NOW()');
        if (($invoice->invoice_date_due < $now) && ($invoice->salesinvoiceamount->invoice_balance <> 0.00) && ($invoice->salesinvoiceamount->invoice_paid < $invoice->salesinvoiceamount->invoice_total)) {
            // Use the overdue template
            $array = [
                       'setting' => $setting->get_setting('pdf_invoice_template_overdue'),
                       'watermark' => $setting->getAssetholderRelativeUrl().'core/img/overdue.png' 
                     ];
            return $array;
        } elseif ($invoice->invoice_status_id == 4) {
            // Use the paid template
            $array = [
                       'setting' => $setting->get_setting('pdf_invoice_template_paid'),
                       'watermark' => $setting->getAssetholderRelativeUrl().'core/img/paid.png' 
                     ];
            return $array;
        } else {
            // Use the default template
            $array = [
                       'setting' => $setting->get_setting('pdf_invoice_template'),
                       'watermark' => null
                     ];
            return $array;
        }
    }

    public static function select_email_invoice_template($invoice, SettingRepository $setting)
    {
        $now = new Expression('NOW()');
        if ($invoice->invoice_date_due < $now) {
            // Use the overdue template
            return $setting->get_setting('email_invoice_template_overdue');
        } elseif ($invoice->invoice_status_id == 4) {
            // Use the paid template
            return $setting->get_setting('email_invoice_template_paid');
        } else {
            // Use the default template
            return $setting->get_setting('email_invoice_template');
        }
    }
}
