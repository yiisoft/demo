<?php
Namespace frontend\modules\invoice\application\helpers;

use frontend\modules\invoice\application\models\ci\Mdl_settings;
use frontend\modules\invoice\application\components\Utilities;
use yii\helpers\FileHelper;
use yii\base\Component;
use Yii;

Class MpdfHelper extends Component
{
        public static function pdf_create($html,$filename,$stream = true) 
        {
            $mdl_settings = new Mdl_settings();
            $mdl_settings->load_settings();
            $invoice_array = [];
            $mpdf_temp_folder = Yii::getAlias('@webroot').Utilities::getMpdfTempfolderRelativeUrl();            
            if (!is_dir($mpdf_temp_folder)){
                FileHelper::createDirectory($mpdf_temp_folder);
            }
            
            $mpdf = new \Mpdf\Mpdf();

            // mPDF configuration
            $mpdf->useAdobeCJK = true;
            $mpdf->autoScriptToLang = true;
            $mpdf->autoVietnamese = true;
            $mpdf->autoArabic = true;
            $mpdf->autoLangToFont = true;

            if (YII_DEBUG) {
                $mpdf->showImageErrors = true;
            }
            
            // @webroot ie. c:/wamp64 etc     .    /frontend/modules/invoice/uploads/archive
            $folder = Yii::getAlias('@webroot').Utilities::getUploadsArchiveholderRelativeUrl();
            // Check if the archive folder is available
            if (!(is_dir($folder) || is_link($folder))) {
                FileHelper::createDirectory($folder);
            }

            // Set the footer if voucher is invoice and if set in settings
            if (!empty($mdl_settings->get_setting('pdf_invoice_footer'))) {
                $mpdf->setAutoBottomMargin = 'stretch';
                $mpdf->SetHTMLFooter('<div id="footer">' . $mdl_settings->get_setting('pdf_invoice_footer') . '</div>');
            }

            // Watermark
            if ($mdl_settings->get_setting('pdf_watermark')) {
                $mpdf->showWatermarkText = true;
            }
            
            $cssFile = Yii::getAlias('@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css');
            $css = file_get_contents($cssFile);
            $mpdf->writeHtml($css,1);
            $mpdf->WriteHTML((string) $html,2);

            foreach (glob(Yii::getAlias('@webroot').Utilities::getUploadsArchiveholderRelativeUrl() . '*' . $filename . '.pdf') as $file) {
                array_push($invoice_array, $file);
            }

            if (!empty($invoice_array)) {
                rsort($invoice_array);

                if ($stream) {
                    return $mpdf->Output($filename . '.pdf', 'I');
                } else {
                    return $invoice_array[0];
                }
            }

            $archived_file = Yii::getAlias('@webroot').Utilities::getUploadsArchiveholderRelativeUrl() .'/'. date('Y-m-d') . '_' . $filename . '.pdf';
            $mpdf->Output($archived_file, 'F');

            if ($stream) {
                return $mpdf->Output($filename . '.pdf', 'I');
            } else {
                return $archived_file;
            }
            
            if ($stream) {
                return $mpdf->Output($filename . '.pdf', 'I');
            } else {
                $mpdf->Output(Yii::getAlias('@webroot').Utilities::getMpdfTempfolderRelativeUrl() . $filename . '.pdf', 'F');
                return Yii::getAlias('@webroot').Utilities::getMpdfTempfolderRelativeUrl() . $filename . '.pdf';
            }
        }
}