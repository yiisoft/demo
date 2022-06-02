<?php
declare(strict_types=1); 

namespace App\Invoice\Helpers;

use App\Invoice\Setting\SettingRepository as SR;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Files\FileHelper;

// ********************************************************
// \Mpdf\Output\Destination::INLINE, or "I"
// send the file inline to the browser. The plug-in is used if available. 
// The name given by $filename is used when one selects the �Save as� option on the link generating the PDF.
// 
// \Mpdf\Output\Destination::DOWNLOAD, or "D"
// send to the browser and force a file download with the name given by $filename.
// 
// \Mpdf\Output\Destination::FILE, or "F"
// save to a local file with the name given by $filename (may include a path).
// 
// \Mpdf\Output\Destination::STRING_RETURN, or "S"
// return the document as a string. $filename is ignored.
// ********************************************************
Class MpdfHelper 
{       
        /**
         * Blank default mode
         */
        const MODE_BLANK = '';
        /**
         * Core fonts mode
         */
        const MODE_CORE = 'c';
        /**
         * Unicode UTF-8 encoded mode
         */
        const MODE_UTF8 = 'UTF-8';
        /**
         * Asian fonts mode
         */
        const MODE_ASIAN = '+aCJK';
        /**
         * A3 page size format
         */
        const FORMAT_A3 = 'A3';
        /**
         * A4 page size format
         */
        const FORMAT_A4 = 'A4';
        /**
         * Letter page size format
         */
        const FORMAT_LETTER = 'Letter';
        /**
         * Legal page size format
         */
        const FORMAT_LEGAL = 'Legal';
        /**
         * Folio page size format
         */
        const FORMAT_FOLIO = 'Folio';
        /**
         * Ledger page size format
         */
        const FORMAT_LEDGER = 'Ledger-L';
        /**
         * Tabloid page size format
         */
        const FORMAT_TABLOID = 'Tabloid';
        /**
         * Portrait orientation
         */
        const ORIENT_PORTRAIT = 'P';
        /**
         * Landscape orientation
         */
        const ORIENT_LANDSCAPE = 'L';
        /**
         * File output sent to browser inline
         */
        const DEST_BROWSER = 'I';
        /**
         * File output sent for direct download
         */
        const DEST_DOWNLOAD = 'D';
        /**
         * File output sent to a file
         */
        const DEST_FILE = 'F';
        /**
         * File output sent as a string
         */
        const DEST_STRING = 'S';
        /**
         * @var string specifies the mode of the new document. If the mode is set by passing a country/language string,
         * this may also set: available fonts, text justification, and directionality RTL.
         */
        public $mode = self::MODE_BLANK;
        /**
         * @var string|array, the format can be specified either as a pre-defined page size, or as an array of width and
         * height in millimetres.
         */
        public $format = self::FORMAT_A4;
        /**
         * @var integer sets the default document font size in points (pt)
         */
        public $defaultFontSize = 0;
        /**
         * @var string sets the default font-family for the new document. Uses default value set in defaultCSS
         * unless codepage has been set to "win-1252". If codepage="win-1252", the appropriate core Adobe font
         * will be set i.e. Helvetica, Times, or Courier.
         */
        public $defaultFont = '';
        /**
         * @var float sets the page left margin for the new document. All values should be specified as LENGTH in
         * millimetres. If you are creating a DOUBLE-SIDED document, the margin values specified will be used for
         * ODD pages; left and right margins will be mirrored for EVEN pages.
         */
        public $marginLeft = 15;
        /**
         * @var float sets the page right margin for the new document (in millimetres).
         */
        public $marginRight = 15;
        /**
         * @var float sets the page top margin for the new document (in millimetres).
         */
        public $marginTop = 16;
        /**
         * @var float sets the page bottom margin for the new document (in millimetres).
         */
        public $marginBottom = 16;
        /**
         * @var float sets the page header margin for the new document (in millimetres).
         */
        public $marginHeader = 9;
        /**
         * @var float sets the page footer margin for the new document (in millimetres).
         */
        public $marginFooter = 9;
        /**
         * @var string specifies the default page orientation of the new document.
         */
        public $orientation = self::ORIENT_PORTRAIT;    
        
        public $options = [
            'autoScriptToLang' => true,
            'ignore_invalid_utf8' => true,
            'tabSpaces' => 4,
        ];
        
        public function pdf_create($html,$filename,$stream,$password,SR $sR, $isInvoice = false, $quote) 
        {
            $sR->load_settings();
            $invoice_array = [];            
            $aliases = $this->ensure_temp_mpdf_folder_and_uploads_folder_exist($sR);  
            $title = ($stream ? $sR::getTempMpdffolderRelativeUrl() . $filename . '.pdf':$filename . '.pdf');
            $start_mpdf = $this->initialize_pdf($password, $sR, $title, $quote);
            $css = $this->get_css_file($aliases);
            $mpdf = $this->write_html_to_pdf($css,$html,$start_mpdf);            
            if ($isInvoice) {
                $this->isInvoice($filename, $invoice_array, $stream, $mpdf, $aliases, $sR); 
            }            
            if ($stream) {
                // send the file inline to the browser. The plug-in is used if available.
                return $mpdf->Output($filename . '.pdf', self::DEST_BROWSER);
            } else {
                // save to a local file with the name given by $filename (may include a path).
                $mpdf->Output($aliases->get('@uploads').$sR::getTempMpdffolderRelativeUrl() . $filename . '.pdf', self::DEST_FILE);
                return $aliases->get('@uploads').$sR::getTempMpdffolderRelativeUrl() . $filename . '.pdf';
            }
        }
        
        private function isInvoice($filename, $invoice_array, $stream, $mpdf, $aliases, $sR) {
            foreach (glob($aliases->get('@uploads').$sR::getUploadsArchiveholderRelativeUrl() . '*' . $filename . '.pdf') as $file) {
                array_push($invoice_array, $file);
            }

            if (!empty($invoice_array)) {
                rsort($invoice_array);

                if ($stream) {
                    return $mpdf->Output($filename . '.pdf', self::DEST_BROWSER);
                } else {
                    return $invoice_array[0];
                }
            }
            // Archive the file if it is an invoice
            $archived_file = $aliases->get('@uploads').$sR::getUploadsArchiveholderRelativeUrl() .'/Invoice/'. date('Y-m-d') . '_' . $filename . '.pdf';
            $mpdf->Output($archived_file, self::DEST_FILE);
            if ($stream) {
                return $mpdf->Output($filename . '.pdf', self::DEST_BROWSER);
            } else {
                return $archived_file;
            }
        }
        
        private function ensure_temp_mpdf_folder_and_uploads_folder_exist($sR) {
            $aliases = new Aliases(['@invoice' => dirname(__DIR__), '@uploads' => '@invoice/Uploads']);
            // Invoice/Uploads/Temp/Mpdf
            $temp_mpdf_folder = $aliases->get('@uploads').$sR::getTempMpdffolderRelativeUrl();            
            if (!is_dir($temp_mpdf_folder)){
                FileHelper::ensureDirectory($temp_mpdf_folder);
            } 
            
            // Invoice/Uploads/Archive
            $folder = $aliases->get('@uploads').$sR::getUploadsArchiveholderRelativeUrl();
            // Check if the archive folder is available
            if (!(is_dir($folder) || is_link($folder))) {
                FileHelper::ensureDirectory($folder);
            }
            return $aliases;
        }
        
        private function initialize_pdf($password, $sR, $title, $quote){
            
            $mpdf = new \Mpdf\Mpdf($this->options);
            // mPDF configuration
            $mpdf->SetDirectionality('ltr');
            $mpdf->useAdobeCJK = true;
            $mpdf->autoScriptToLang = true;
            $mpdf->autoVietnamese = true;
            $mpdf->allow_charset_conversion = false;
            $mpdf->autoArabic = true;
            $mpdf->autoLangToFont = true;
            $mpdf->SetTitle($title);
            $mpdf->showImageErrors = true; 
            
            $content = $title. ': '. Date($sR->trans('date_format'));
            $mpdf->SetHTMLHeader('<div style="text-align: right; font-size: 8px; font-weight: lighter;">'.$content.'</div>');

            // Set the footer if is invoice and if set in settings
            if (!empty($sR->get_setting('pdf_invoice_footer'))) {
                $mpdf->setAutoBottomMargin = 'stretch';
                $mpdf->SetHTMLFooter('<div id="footer">' . $sR->get_setting('pdf_invoice_footer') . '</div>');
            }

            // Watermark
            if (!empty($sR->get_setting('pdf_watermark'))) {
                $mpdf->showWatermarkText = true;
            }
            
            if (($sR->get_folder_language() === "Arabic") || ($quote->getClient()->getClient_language() === "Arabic")) {
                $mpdf->SetDirectionality('rtl');         
            }
            
            // Set a password if set for the voucher
            if (!empty($password)) {
                $mpdf->SetProtection(['copy', 'print'], $password, $password);
            }            
            return $mpdf;
        }
        
        private function get_css_file($aliases){
            $cssFile = $aliases->get('@invoice/Asset/kartik-v/kv-mpdf-bootstrap.min.css');
            return file_get_contents($cssFile);
        }
        
        private function write_html_to_pdf($css,$html,$mpdf){
            $mpdf->writeHtml($css,1);
            $mpdf->WriteHTML((string)$html,2);
            return $mpdf;
        }
        
        /**
         * Acknowledgement to yii2-mpdf
         * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2020
         * @package yii2-mpdf
         * @version 1.0.6
         */

        private function options(){
            $this->options['mode'] = $this->mode;
            $this->options['format'] = $this->format;
            $this->options['default_font_size'] = $this->defaultFontSize;
            $this->options['default_font'] = $this->defaultFont;
            $this->options['margin_left'] = $this->marginLeft;
            $this->options['margin_right'] = $this->marginRight;
            $this->options['margin_top'] = $this->marginTop;
            $this->options['margin_bottom'] = $this->marginBottom;
            $this->options['margin_header'] = $this->marginHeader;
            $this->options['margin_footer'] = $this->marginFooter;
            $this->options['orientation'] = $this->orientation;
        }
}