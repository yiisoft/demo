<?php
declare(strict_types=1); 

Namespace App\Invoice\Helpers;

// Repositories
use App\Invoice\Setting\SettingRepository as SRepo;
use App\Invoice\Client\ClientRepository as CR;
use App\Invoice\CustomValue\CustomValueRepository as CVR;
use App\Invoice\CustomField\CustomFieldRepository as CFR;
use App\Invoice\Inv\InvRepository as IR;
use App\Invoice\InvCustom\InvCustomRepository as ICR;
use App\Invoice\InvItem\InvItemRepository as IIR;
use App\Invoice\InvAmount\InvAmountRepository as IAR;
use App\Invoice\InvItemAmount\InvItemAmountRepository as IIAR;
use App\Invoice\InvTaxRate\InvTaxRateRepository as ITRR;
use App\Invoice\Quote\QuoteRepository as QR;
use App\Invoice\QuoteAmount\QuoteAmountRepository as QAR;
use App\Invoice\QuoteCustom\QuoteCustomRepository as QCR;
use App\Invoice\QuoteItemAmount\QuoteItemAmountRepository as QIAR;
use App\Invoice\QuoteItem\QuoteItemRepository as QIR;
use App\Invoice\QuoteTaxRate\QuoteTaxRateRepository as QTRR;
use App\Invoice\UserInv\UserInvRepository as UIR;
use App\Invoice\Helpers\PdfHelper;
use App\Invoice\Helpers\TemplateHelper;
use App\Invoice\Helpers\InvoiceHelper;
use Yiisoft\Html\Html;
use Yiisoft\Router\UrlGeneratorInterface as UrlGenerator;
use Yiisoft\Security\Crypt;
use Yiisoft\Session\SessionInterface as Session;
use Yiisoft\Yii\View\ViewRenderer;
use \PHPMailer\PHPMailer\PHPMailer;

Class MailerHelper
{

private SRepo $s;
private Session $session;
private PdfHelper $pdfhelper;
private TemplateHelper $templatehelper;
private InvoiceHelper $invoicehelper;
    
public function __construct(SRepo $s, Session $session)
{
    $this->s = $s;
    $this->session = $session;
    $this->pdfhelper = new PdfHelper($s, $session);
    $this->templatehelper = new TemplateHelper();
    $this->invoicehelper = new InvoiceHelper();
    $this->phpmailer = new PHPMailer();
    $this->crypt = new Crypt();
}
    
    public function mailer_configured()
    {
        return (($this->s->setting('email_send_method') == 'phpmail') ||
            ($this->s->setting('email_send_method') == 'sendmail') ||
            (($this->s->setting('email_send_method') == 'smtp') && ($this->s->setting('smtp_server_address')))
        );
    }

    /**
     * Send an invoice via email
     *
     * @param $inv_id
     * @param $from
     * @param $to
     * @param $subject
     * @param string $body
     * @param null $cc
     * @param null $bcc
     * @param null $attachments
     * @param $custom
     * @return bool
     */
    public function email_invoice($inv_id, $from, $to, $subject, $body, $cc, $bcc, $attachments, $custom,
        CR $cR, CFR $cfR, CVR $cvR, IAR $iaR, ICR $icR, IIAR $iiaR, IIR $iiR, IR $iR, ITRR $itrR, UIR $uiR, ViewRenderer $viewrenderer) : bool
    {
        $inv_amount = (($iaR->repoInvAmountCount($inv_id) > 0) ? $iaR->repoInvquery($inv_id) : null);
        $inv_custom_values = $this->inv_custom_values($this->session->get('inv_id'),$icR);
        $inv = $iR->repoInvUnLoadedquery($inv_id);
        $stream = false;
        if ($inv->getSumex_id() == null) {
            $invoice = $this->pdfhelper->generate_inv_pdf($inv_id,$inv->getUser_id(), $stream, $custom, $inv_amount, $inv_custom_values,
                       $cR, $cvR, $cfR, $iiR, $iiaR, $iR, $itrR, $uiR, $viewrenderer); 
        } else {
            // TODO
            $invoice = $this->pdfhelper->generate_inv_sumex();
        }
        $mail_message = $this->templatehelper->parse_template($inv, $body);
        $mail_subject = $this->templatehelper->parse_template($inv, $subject);
        $mail_cc = $this->templatehelper->parse_template($inv, $cc);
        $mail_bcc = $this->templatehelper->parse_template($inv, $bcc);
        $mail_from = array($this->templatehelper->parse_template($inv, $from[0]), parse_template($inv, $from[1]));
        $message = (empty($mail_message) ? ' ' : $mail_message);

        return $this->phpmail_send($mail_from, $to, $mail_subject, $message, $invoice, $mail_cc, $mail_bcc, $attachments);
    }

    /**
     * Send a quote via email
     *
     * @param $quote_id
     * @param $from
     * @param $to
     * @param $subject
     * @param string $body
     * @param null $cc
     * @param null $bcc
     * @param null $attachments
     * @return bool
     */
    function email_quote($quote_id, $from, $to, $subject, $body, $cc, $bcc, $attachments, $custom,
        CR $cR, CFR $cfR, CVR $cvR, QAR $qaR, QCR $qcR, QIAR $qiaR, QIR $qiR, QR $qR, QTRR $qtrR, UIR $uiR, ViewRenderer $viewrenderer)                
            
     {
        $db_quote = $qiR->repoQuoteUnLoadedquery($quote_id);
        $quote_amount = (($qaR->repoQuoteAmountCount($quote_id) > 0) ? $qaR->repoQuotequery($quote_id) : null);        
        $quote_custom_values = $this->quote_custom_values($this->session->get('quote_id'),$qcR);
        $stream = false;
        $quote = $this->pdfhelper->generate_quote_pdf($quote_id, $quote->getUser_id(), $stream, $custom, $quote_amount, $quote_custom_values,
                                                      $cR, $cvR, $cfR, $qiR, $qiaR, $qR, $qtrR, $uiR, $viewrenderer); 
        $message = $this->templatehelper->parse_template($db_quote, $body);
        $mail_subject = $this->templatehelper->parse_template($db_quote, $subject);
        $mail_cc = $this->templatehelper->parse_template($db_quote, $cc);
        $mail_bcc = $this->templatehelper->parse_template($db_quote, $bcc);
        $mail_from = array($this->templatehelper->parse_template($db_quote, $from[0]), parse_template($db_quote, $from[1]));

        $mail_message = (empty($message) ? ' ' : $message);

        return $this->phpmail_send($mail_from, $to, $mail_subject, $mail_message, $quote, $mail_cc, $mail_bcc, $attachments);
    }

    /**
     * Send an email if the status of an email changed
     * @param $quote_id
     * @param string $status string "accepted" or "rejected"
     * @return bool if the email was sent
     */
    function email_quote_status($quote_id, $status, QIR $qiR, UIR $uiR, UrlGenerator $urlGenerator)
    {
        if (!$this->mailer_configured()) {
            return false;
        }
        $quote = $qiR->repoQuoteLoadedquery($quote_id);
        $base_url = $urlGenerator->generate('quote/view',['id'=>$quote_id]);
        $user_inv = $uiR->repoUserInvquery($quote_id);
        $user_email = $user_inv->getEmail();
        $subject = sprintf($this->s->trans('quote_status_email_subject'),
            $quote->getClient_name(),
            strtolower($this->phpmailer->lang($status)),
            $quote->getNumber()
        );
        $body = sprintf(nl2br($this->s->trans('quote_status_email_body')),
            $quote->getClient()->getClient_name(),
            strtolower($this->phpmailer->lang($status)),
            $quote->getNumber(),Html::a($base_url,$base_url)
        );
        return $this->phpmail_send($user_email, $user_email, $subject, $body);
    }
    
    /**
     * @param $from
     * @param $to
     * @param $subject
     * @param $message
     * @param null $attachment_path
     * @param null $cc
     * @param null $bcc
     * @param null $more_attachments
     * @return bool
     */
    private function phpmail_send(
        $from,
        $to,
        $subject,
        $message,
        $attachment_path,
        $cc,
        $bcc,
        $more_attachments,
        UIR $uiR
    ) {
        // Create the basic mailer object
        $mail = new PHPMailer();
        $mail->CharSet = 'UTF-8';
        $mail->isHTML();

        switch ($this->s->get_setting('email_send_method')) {
            case 'smtp':
                $mail->IsSMTP();

                // Set the basic properties
                $mail->Host = $this->s->get_setting('smtp_server_address');
                $mail->Port = $this->s->get_setting('smtp_port');

                // Is SMTP authentication required?
                if ($this->s->get_setting('smtp_authentication')) {
                    $mail->SMTPAuth = true;
                    
                    //decrypt($data, bool $passwordBased, string $secret, string $info = '')
                    $decoded = $this->crypt->decrypt($this->s->get_setting('smtp_password'),false,'my_secret','');

                    $mail->Username = $this->s->get_setting('smtp_username');
                    $mail->Password = $decoded;
                }

                // Is a security method required?
                if ($this->s->get_setting('smtp_security')) {
                    $mail->SMTPSecure = $this->s->get_setting('smtp_security');
                }

                // Check if certificates should not be verified
                if (!$this->s->get_setting('smtp_verify_certs', true)) {
                    $mail->SMTPOptions = array(
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    );
                }

                break;
            case 'sendmail':
                $mail->IsMail();
                break;
            case 'phpmail':
            case 'default':
                $mail->IsMail();
                break;
        }

        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->AltBody = $mail->normalizeBreaks($mail->html2text($message));

        if (is_array($from)) {
            // This array should be address, name
            $mail->setFrom($from[0], $from[1]);
        } else {
            // This is just an address
            $mail->setFrom($from);
        }

        // Allow multiple recipients delimited by comma or semicolon
        $mail_to = (strpos($to, ',')) ? explode(',', $to) : explode(';', $to);

        // Add the addresses
        foreach ($mail_to as $address) {
            $mail->addAddress($address);
        }

        if ($cc) {
            // Allow multiple CC's delimited by comma or semicolon
            $cc = (strpos($cc, ',')) ? explode(',', $cc) : explode(';', $cc);

            // Add the CC's
            foreach ($cc as $address) {
                $mail->addCC($address);
            }
        }

        if ($bcc) {
            // Allow multiple BCC's delimited by comma or semicolon
            $bcc = (strpos($bcc, ',')) ? explode(',', $bcc) : explode(';', $bcc);
            // Add the BCC's
            foreach ($bcc as $address) {
                $mail->addBCC($address);
            }
        }
        
        // Bcc mails to admin && the admin email account has been setup under userinv which is an extension table of user
        if (($this->s->get_setting('bcc_mails_to_admin') == 1) && ($uiR->repoUserInvUserIdcount((string)1) > 0)) {
            // Get email address of admin account and push it to the array
            $user_inv = $uiR->repoUserInvUserIdquery((string)1);
            $mail->addBCC($user_inv->getEmail());
        }

        // Add the attachment if supplied
        if ($attachment_path && $this->s->get_setting('email_pdf_attachment')) {
            $mail->addAttachment($attachment_path);
        }
        // Add the other attachments if supplied
        if ($more_attachments) {
            foreach ($more_attachments as $paths) {
                $mail->addAttachment($paths['path'], $paths['filename']);
            }
        }

        // And away it goes...
        if ($mail->send()) {
            $this->flash('success', 'The email has been sent');
            return true;
        } else {
            // Or not...
            $this->flash('warning', $mail->ErrorInfo);
            return false;
        }
    }
    
    private function flash($level, $message){
        $flash = new Flash($this->session);
        $flash->set($level, $message); 
        return $flash;
    }
}