<?php

declare(strict_types=1); 

namespace App\Invoice\InvRecurring;

use App\Invoice\Entity\InvRecurring;
use App\Invoice\Inv\InvService as IS;
use App\Invoice\InvRecurring\InvRecurringService;
use App\Invoice\InvRecurring\InvRecurringRepository;
use App\Invoice\Setting\SettingRepository as sR;
use App\Invoice\Helpers\DateHelper;
use App\Invoice\Helpers\NumberHelper;
use App\Invoice\Helpers\MailerHelper;
use App\User\UserService;
use App\Service\WebControllerService;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\DataResponse\DataResponseFactoryInterface;
use Yiisoft\Http\Method;
use Yiisoft\Json\Json;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Session\SessionInterface as Session;
use Yiisoft\Session\Flash\Flash;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\View\ViewRenderer;
use \Exception;
use DateTimeImmutable;

final class InvRecurringController
{
    private DataResponseFactoryInterface $factory;
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService;
    private InvRecurringService $invrecurringService;    
    private sR $s;
    private IS $iS;
    private TranslatorInterface $translator;
        
    public function __construct(
        DataResponseFactoryInterface $factory,    
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UserService $userService,
        InvRecurringService $invrecurringService,
        Session $session,
        SR $s,
        IS $iS,
        TranslatorInterface $translator
    )    
    {
        $this->factory = $factory;
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/invrecurring')
                                           ->withLayout(dirname(dirname(__DIR__)).'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->userService = $userService;
        $this->invrecurringService = $invrecurringService;
        $this->session = $session;
        $this->s = $s;
        $this->iS = $iS;
        $this->translator = $translator;
    }
    
    private function body($invrecurring) {
        $body = [                
          'id'=>$invrecurring->getId(),
          'inv_id'=>$invrecurring->getInv_id(),
          'start'=>$invrecurring->getStart(),
          'end'=>$invrecurring->getEnd(),
          'frequency'=>$invrecurring->getFrequency(),
          'next'=>$invrecurring->getNext()
        ];
        return $body;
    }
    
    //inv.js create_recurring_confirm function calls this function
    public function create_recurring_confirm(Request $request, ValidatorInterface $validator) : Response {
        $this->rbac();
        $body = $request->getQueryParams() ?? [];
        $form = new InvRecurringForm();
        $invrecurring = new InvRecurring(); 
        $body_array = [
            'inv_id'=>$body['inv_id'],
            'start'=>$body['recur_start_date'] ?? null,
            'end'=>$body['recur_end_date'] ?? null,
            'frequency'=>$body['recur_frequency'],
            // The next invoice date is the new recur start date
            'next'=>$body['recur_start_date'] ?? null
        ];
        if ($form->load($body_array) && $validator->validate($form)->isValid()) {    
                $this->invrecurringService->saveInvRecurring($invrecurring,$form);
                 $parameters = ['success'=>1];
           //return response to inv.js to reload page at location
            return $this->factory->createResponse(Json::encode($parameters));          
        } else {
            $parameters = [
               'success'=>0,
            ];
            //return response to quote.js to reload page at location
            return $this->factory->createResponse(Json::encode($parameters));          
        } 
    }
    
    // If a cron has been setup on your server: eg. https:\\invoice.myhost\invoice\invrecurring\recur\UIJYTYJHGYTYGFFDT$%GHF
    public function recur($cron_key, 
            SessionInterface $session,
            ICR $icR, IIAR $iiaR, IIAS $iiaS, IIR $iiR, IS $iS, IR $iR,IAR $iaR, ITRR $itrR, 
            MailerHelper $mailerhelper, GR $gR, PR $pR, TRR $trR, Validator $validator) : void
    {
        // Check the provided cron key
        if ($cron_key != $this->s->get_setting('cron_key')) {
            $flash = $this->flash($session, 'warning' ,$this->s->trans('generate'). " ".$this->s->trans('cron_key'));
            exit;
        }

        // Gather a list of recurring invoices to generate
        $invoices_recurring = $iR->active();

        foreach ($invoices_recurring as $invoice_recurring) {
            // This is the original invoice id
            $source_id = $invoice_recurring->getInv_id();

            // This is the original invoice
            $invoice = $iR->repoInvUnLoadedquery($source_id);

            // Create the new invoice
            $array = [
                'client_id' => $invoice->getClient_id(),
                'invoice_date_created' => $invoice_recurring->getNext_date(),
                'invoice_date_due' => $iR->get_date_due($invoice_recurring->getNext_date()),
                'invoice_group_id' => $invoice->getGroup_id(),
                'user_id' => $invoice->getUser_id(),
                'invoice_number' => $iR->get_invoice_number($invoice->getGroup_id()),
                'invoice_url_key' => $iR->get_url_key(),
                'invoice_terms' => $invoice->getInvoice_terms()
            ];

            // This is the new invoice id
            $inv = new Inv();
            $target_id = $iS->saveInv($this->userService->getUser(),$inv,$array, $this->sR, $gR, $iaR);

            // Copy the original invoice to the new invoice
            $iR->copy_invoice($source_id, $target_id, false, $iaR, $icR, $iiaR, $iiaS, $pR, $iiR, $itrR, $trR, $validator);

            // Update the next recur date for the recurring invoice
            $this->set_next_recur_date($invoice_recurring->getId());

            // Email the new invoice if applicable
            if ($this->s->get_setting('automatic_email_on_recur') && $mailerhelper->mailer_configured()) {
                $new_invoice = $iR->repoInvUnLoadedquery($target_id);

                // Set the email body, use default email template if available
                $this->load->model('email_templates/mdl_email_templates');

                $email_template_id = $s->get_setting('email_invoice_template');
                if (!$email_template_id) {
                    log_message('error', '[Recurring Invoices] No email template set in the system settings!');
                    continue;
                }

                $email_template = $this->mdl_email_templates->where('email_template_id', $email_template_id)->get();
                if ($email_template->num_rows() == 0) {
                    log_message('error', '[Recurring Invoices] No email template set in the system settings!');
                    continue;
                }

                $tpl = $email_template->row();

                // Prepare the attachments
                $this->load->model('upload/mdl_uploads');
                $attachment_files = $this->mdl_uploads->get_invoice_uploads($target_id);

                // Prepare the body
                $body = $tpl->email_template_body;
                if (strlen($body) != strlen(strip_tags($body))) {
                    $body = htmlspecialchars_decode($body);
                } else {
                    $body = htmlspecialchars_decode(nl2br($body));
                }

                $from = !empty($tpl->email_template_from_email) ?
                    array($tpl->email_template_from_email, $tpl->email_template_from_name) :
                    array($invoice->user_email, "");

                $subject = !empty($tpl->email_template_subject) ?
                    $tpl->email_template_subject :
                    trans('invoice') . ' #' . $new_invoice->invoice_number;

                $pdf_template = $tpl->email_template_pdf_template;
                $to = $invoice->client_email;
                $cc = $tpl->email_template_cc;
                $bcc = $tpl->email_template_bcc;

                $email_invoice = email_invoice($target_id, $pdf_template, $from, $to, $subject, $body, $cc, $bcc, $attachment_files);

                if ($email_invoice) {
                    $this->mdl_invoices->mark_sent($target_id);
                    $this->mdl_invoice_amounts->calculate($target_id);
                } else {
                    log_message('error', '[Recurring Invoices] Invoice ' . $target_id . 'could not be sent. Please review your Email settings.');
                }
            }
        }

        log_message('debug', '[Recurring Invoices] ' . count($invoices_recurring) . ' recurring invoices processed');
    }  
    
    
    /**
     * @param $invoice_recurring_id
     */
    public function set_next_recur_date($invoice_recurring_id, IR $iR)
    {
        $invoice_recurring = $iR->repoInvRecurringquery($invoice_recurring_id);

        $recur_next_date = increment_date($invoice_recurring->getNext_date(), $invoice_recurring->getFrequency());

        $db_array = array(
            'recur_next_date' => $recur_next_date
        );

        $this->db->where('invoice_recurring_id', $invoice_recurring_id);
        $this->db->update('ip_invoices_recurring', $db_array);
    }
    
    public function stop(CurrentRoute $currentRoute, InvRecurringRepository $iR) {
        $ivr = $iR->repoInvRecurringquery($this->invrecurring($currentRoute, $iR)->getId());
        $ivr->setEnd(date('Y-m-d'));
        $ivr->setNext('0000-00-00');
        $iR->save($ivr);
        return $this->webService->getRedirectResponse('invrecurring/index');
    }
    
    // Used in inv.js get_recur_start_date to pass the frequency determined start date back to the modal 
    public function get_recur_start_date(Request $request, sR $s){
        $body = $request->getQueryParams() ?? [];
        $invoice_date = $body['invoice_date'];
        // DateTimeImmutable::__construct(): Failed to parse time string (22-04-202222-04-2022) at position 10 (2): Double date specification
        $sub_str = substr($invoice_date,0,10);
        $immutable_invoice_date = new DateTimeImmutable($sub_str);
        $recur_frequency = $body['recur_frequency'];
        $dateHelper = new DateHelper($s);
        $parameters = [
                    'success'=>1,
                    // Calculate the recur_start_date in DateTime format.
                    'recur_start_date'=>$dateHelper->increment_user_date($immutable_invoice_date, $recur_frequency)
        ];
        return $this->factory->createResponse(Json::encode($parameters));       
    }
    
    public function delete_recurring() {
        
    }
    
    public function edit(ViewRenderer $head, Session $session, Request $request, CurrentRoute $currentRoute, 
                        ValidatorInterface $validator,
                        InvRecurringRepository $invrecurringRepository, 
                        sR $sR,                        

    ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => 'Edit',
            'action' => ['invrecurring/edit', ['id' => $this->invrecurring($currentRoute, $invrecurringRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->invrecurring($currentRoute, $invrecurringRepository)),
            'head'=>$head,
            's'=>$sR,
            
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new InvRecurringForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->invrecurringService->saveInvRecurring($this->invrecurring($currentRoute,$invrecurringRepository), $form);
                return $this->webService->getRedirectResponse('invrecurring/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function delete(Session $session, CurrentRoute $currentRoute,InvRecurringRepository $invrecurringRepository 
    ): Response {
        $this->rbac($session);
        try {
            $this->invrecurringService->deleteInvRecurring($this->invrecurring($currentRoute,$invrecurringRepository));               
            $this->flash($session, 'info', 'Deleted.');
            return $this->webService->getRedirectResponse('invrecurring/index'); 
	} catch (Exception $e) {
            //unset($e);
            $this->flash($session, 'danger', $e);
            return $this->webService->getRedirectResponse('invrecurring/index'); 
        }
    }
    
    private function invrecurring(CurrentRoute $currentRoute,InvRecurringRepository $invrecurringRepository) 
    {
        //$id = $request->getAttribute('id');
        $id = $currentRoute->getArgument('id');       
        $invrecurring = $invrecurringRepository->repoInvRecurringquery($id);
        if ($invrecurring === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $invrecurring;
    }
    
    private function invrecurrings(InvRecurringRepository $invrecurringRepository) 
    {
        $invrecurrings = $invrecurringRepository->findAllPreloaded();        
        if ($invrecurrings === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $invrecurrings;
    }
           
    private function rbac() 
    {
        $canEdit = $this->userService->hasPermission('editInvRecurring');
        if (!$canEdit){
            $this->flash($this->session,'warning', $this->translator->translate('invoice.permission'));
            return $this->webService->getRedirectResponse('invrecurring/index');
        }
        return $canEdit;
    }
    
    public function view(Session $session, CurrentRoute $currentRoute,InvRecurringRepository $invrecurringRepository,
        SettingRepository $sR,
        ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $sR->trans('view'),
            'action' => ['invrecurring/view', ['id' => $this->invrecurring($currentRoute, $invrecurringRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->invrecurring($currentRoute, $invrecurringRepository)),
            's'=>$sR,             
            'invrecurring'=>$invrecurringRepository->repoInvRecurringquery($this->invrecurring($currentRoute, $invrecurringRepository)->getId()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }
    
    public function index(Session $session, CurrentRoute $currentRoute, InvRecurringRepository $iR, sR $sR): Response
    {
        $pageNum = (int)$currentRoute->getArgument('page', '1');
        $paginator = (new OffsetPaginator($this->invrecurrings($iR)))
        ->withPageSize((int)$sR->setting('default_list_limit'))
        ->withCurrentPage($pageNum);
        $numberhelper = new NumberHelper($sR);
        $canEdit = $this->rbac($session);
        $flash = $this->flash($session, '','');
        $parameters = [        
                'paginator'=>$paginator,
                's'=>$sR,
                'canEdit' => $canEdit,
                'recur_frequencies'=>$numberhelper->recur_frequencies(), 
                'invrecurrings'=>$this->invrecurrings($iR),
                'flash'=> $flash
        ];
        return $this->viewRenderer->render('index', $parameters);  
    }
    
    public function add(ViewRenderer $head,Session $session, Request $request, 
                        ValidatorInterface $validator,
                        sR $sR,                        

    ) : Response
    {
        $this->rbac($session);
        $parameters = [
            'title' => 'Add',
            'action' => ['invrecurring/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$sR,
            'head'=>$head,
            
        ];
        
        if ($request->getMethod() === Method::POST) {
            
            $form = new InvRecurringForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->invrecurringService->saveInvRecurring(new InvRecurring(),$form);
                return $this->webService->getRedirectResponse('invrecurring/index');
            }
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    private function flash(Session $session, $level, $message){
        $flash = new Flash($session);
        $flash->set($level, $message); 
        return $flash;
    }    
}

