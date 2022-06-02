<?php
declare(strict_types=1); 

namespace App\Invoice\Inv;
// Entity's
use App\Invoice\Entity\Inv;
use App\Invoice\Entity\InvItem;
use App\Invoice\Entity\InvAmount;
use App\Invoice\Entity\InvCustom;
use App\Invoice\Entity\InvTaxRate;
// Services
// Inv
use App\User\UserService;
use App\Invoice\Inv\InvService;
use App\Invoice\InvItem\InvItemService;
use App\Invoice\InvAmount\InvAmountService;
use App\Invoice\InvItemAmount\InvItemAmountService as IIAS;
use App\Invoice\InvTaxRate\InvTaxRateService;
use App\Invoice\InvCustom\InvCustomService;
use App\Service\WebControllerService;
// Forms Inv
use App\Invoice\Inv\InvForm;
use App\Invoice\InvCustom\InvCustomForm;
use App\Invoice\InvItem\InvItemForm;
use App\Invoice\InvTaxRate\InvTaxRateForm;
// Repositories
use App\Invoice\Client\ClientRepository as CR;
use App\Invoice\CustomValue\CustomValueRepository as CVR;
use App\Invoice\CustomField\CustomFieldRepository as CFR;
use App\Invoice\Family\FamilyRepository as FR;
use App\Invoice\Group\GroupRepository as GR;
use App\Invoice\Inv\InvRepository as IR;
use App\Invoice\InvCustom\InvCustomRepository as ICR;
use App\Invoice\InvItem\InvItemRepository as IIR;
use App\Invoice\InvAmount\InvAmountRepository as IAR;
use App\Invoice\InvItemAmount\InvItemAmountRepository as IIAR;
use App\Invoice\InvRecurring\InvRecurringRepository as IRR;
use App\Invoice\Payment\PaymentRepository as PYMR;
use App\Invoice\PaymentMethod\PaymentMethodRepository as PMR;
use App\Invoice\Product\ProductRepository as PR;
use App\Invoice\Setting\SettingRepository as SR;
use App\Invoice\TaxRate\TaxRateRepository as TRR;
use App\Invoice\InvTaxRate\InvTaxRateRepository as ITRR;
use App\Invoice\Unit\UnitRepository as UNR;
use App\Invoice\UserInv\UserInvRepository as UIR;
use App\User\UserRepository as UR;
// App Helpers
Use App\Invoice\Helpers\DateHelper;
use App\Invoice\Helpers\PdfHelper;
use App\Invoice\Helpers\NumberHelper;
use App\Invoice\Helpers\CustomValuesHelper as CVH;
// Yii
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\DataResponse\DataResponseFactoryInterface;
use Yiisoft\Http\Method;
use Yiisoft\Json\Json;
use Yiisoft\Router\FastRoute\UrlGenerator;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Security\Random;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Session\Flash\Flash;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\View\ViewRenderer;
// Psr\Http
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
// Miscellaneous
use \Exception;
use \DateTimeImmutable;

final class InvController
{
    private DataResponseFactoryInterface $factory;
    private NumberHelper $number_helper; 
    private InvAmountService $inv_amount_service;    
    private InvCustomService $inv_custom_service;
    private InvService $inv_service;
    private InvItemService $inv_item_service;
    private IIAS $inv_item_amount_service;
    private InvTaxRateService $inv_tax_rate_service;
    private SessionInterface $session;
    private SR $sR;
    private TranslatorInterface $translator;
    private UrlGenerator $url_generator;
    private UserService $user_service;
    private ViewRenderer $view_renderer;
    private WebControllerService $web_service;
    
    public function __construct(
        DataResponseFactoryInterface $factory,
        InvAmountService $inv_amount_service,
        InvService $inv_service,
        InvCustomService $inv_custom_service,
        InvItemService $inv_item_service,
        IIAS $inv_item_amount_service,
        InvTaxRateService $inv_tax_rate_service,
        SessionInterface $session,
        SR $sR,
        TranslatorInterface $translator,
        UserService $user_service,        
        UrlGenerator $url_generator,
        ViewRenderer $view_renderer,
        WebControllerService $web_service,                        
    )    
    {
        $this->date_helper = new DateHelper($sR);
        $this->factory = $factory;
        $this->inv_amount_service = $inv_amount_service;
        $this->inv_service = $inv_service;
        $this->inv_custom_service = $inv_custom_service;
        $this->inv_item_service = $inv_item_service;
        $this->inv_item_amount_service = $inv_item_amount_service;
        $this->inv_tax_rate_service = $inv_tax_rate_service;
        $this->number_helper = new NumberHelper($sR);
        $this->session = $session;
        $this->sR = $sR;
        $this->translator = $translator;        
        $this->user_service = $user_service;
        $this->url_generator = $url_generator;        
        $this->view_renderer = $view_renderer->withControllerName('invoice/inv')
                                             ->withLayout(dirname(dirname(__DIR__)) .'/Invoice/Layout/main.php');
        $this->web_service = $web_service;
    }
    
    public function add(ViewRenderer $head, Request $request, 
                        ValidatorInterface $validator,
                        IR $invRepo,
                        CR $clientRepo,
                        GR $groupRepo,
                        UR $userRepo,
                        IAR $iaR,
    ) : Response
    {
        $this->rbac();
        $parameters = [
            'title' => 'Add',
            'action' => ['inv/add'],
            'errors' => [],
            'body' =>$request->getParsedBody(),
            's'=>$this->sR,
            'head'=>$head,
            'invs'=>$invRepo->findAllPreloaded(),
            'inv_statuses'=> $this->sR->getStatuses(),  
            'clients'=>$clientRepo->findAllPreloaded(),
            'groups'=>$groupRepo->findAllPreloaded(),
            'users'=>$userRepo->findAll()
        ];        
        if ($request->getMethod() === Method::POST) {            
            $form = new InvForm();
            $parameters['body']['number'] = $groupRepo->generate_invoice_number($parameters['body']['group_id'], true); 
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->inv_service->saveInv($this->user_service->getUser(),new Inv(),$form,$this->sR,$groupRepo, $iaR);
                return $this->web_service->getRedirectResponse('inv/index');
            }
            $parameters['errors'] = $form->getFormErrors();
        }
        if ($clientRepo->count() > 0) {
            return $this->view_renderer->render('_form', $parameters);
        } else {
            return $this->factory->createResponse($this->view_renderer->renderPartialAsString('/invoice/setting/unsuccessful',
            ['heading'=>'','message'=>$this->sR->trans('add_client'),'url'=>'quote/index']));             
        } 
    }
    
    public function archive(Request $request){            
        // TODO filter system: Currently the filter is disabled on the archive view.
        if ($request->getMethod() === Method::POST) { 
            $body = $request->getParsedBody(); 
            foreach ($body as $key => $value) {
                if (((string)$key === 'invoice_number')) {
                   $invoice_archive = $this->sR->get_invoice_archived_files_with_filter($value);
                   $flash_message = $value;
                }
            }
        } else {
            $invoice_archive = $this->sR->get_invoice_archived_files_with_filter('');
            $flash_message = '';
        }
        $parameters = [            
                's'=>$this->sR,
                'partial_inv_archive'=>$this->view_renderer->renderPartialAsString('partial_inv_archive',
                        [ 
                            's'=>$this->sR,
                            'invoices_archive'=>$invoice_archive
                        ]),           
                'flash'=>$this->flash('',''.$flash_message),
                'body'=>$request->getParsedBody(),
        ];        
        return $this->view_renderer->render('archive', $parameters);
        
    }
    
    private function body($inv) {
        $body = [
          'number'=>$inv->getNumber(),
            
          'id'=>$inv->getId(),
          'user_id'=>$inv->getUser_id(),
          
          'client_id'=>$inv->getClient_id(),          
         
          'date_created'=>$inv->getDate_created(),
          'date_modified'=>$inv->getDate_modified(),
          'date_due'=>$inv->getDate_due(),            
            
          'group_id'=>$inv->getGroup_id(),
          'status_id'=>$inv->getStatus_id(),
          'creditinvoice_parent_id'=>$inv->getCreditinvoice_parent_id(),
          
          'discount_amount'=>$inv->getDiscount_amount(),
          'discount_percent'=>$inv->getDiscount_percent(),
          'url_key'=>$inv->getUrl_key(),
          'password'=>$inv->getPassword(),
          
          'payment_method'=>$inv->getPayment_method(),
          'terms'=>$inv->getTerms()  
            
        ];
        return $body;
    }
        
    // Data fed from inv.js->$(document).on('click', '#inv_create_confirm', function () {
    public function create_confirm(Request $request, ValidatorInterface $validator, GR $gR, TRR $trR, IAR $iaR) : Response
    {
        $this->rbac();  
        $body = $request->getQueryParams() ?? [];        
        $ajax_body = [
            'quote_id'=>null,
            'client_id'=>$body['client_id'],
            'group_id'=>$body['group_id'],
            'creditinvoice_parent_id'=>null ,
            'status_id'=>1,
            'number'=>$this->sR->get_setting('generate_invoice_number_for_draft') === 1 ? $gR->generate_invoice_number($body['group_id'], true):'',
            'discount_amount'=>floatval(0),
            'discount_percent'=>floatval(0),
            'url_key'=>Random::string(32),
            'password'=>$body['inv_password'], 
            'payment_method'=>null!==$this->sR->get_setting('default_payment_method') ? $this->sR->get_setting('default_payment_method') : 0, 
            'terms'=>$this->sR->get_setting('default_invoice_terms') ?? '',
        ];
        $ajax_content = new InvForm();
        $inv = new Inv();
        $invamount = new InvAmount();
        if ($ajax_content->load($ajax_body) && $validator->validate($ajax_content)->isValid()) {    
            $this->inv_service->saveInv($this->user_service->getUser(),$inv,$ajax_content, $this->sR, $gR, $iaR);
            $this->inv_amount_service->initializeInvAmount($invamount, $inv->getId());
            $this->default_taxes($inv, $trR, $validator);
            $parameters = ['success'=>1];
           //return response to inv.js to reload page at location
            return $this->factory->createResponse(Json::encode($parameters));          
        } else {
            $parameters = [
               'success'=>0,
            ];
            //return response to inv.js to reload page at location
            return $this->factory->createResponse(Json::encode($parameters));          
        } 
    }
    
    // Reverse an invoice with a credit invoice/ debtor/client/customer credit note
    public function create_credit_confirm(Request $request, ValidatorInterface $validator,IR $iR, GR $gR, IAR $iaR, IIR $iiR, IIAR $iiaR) : Response {
            $this->rbac();  
            $body = $request->getQueryParams() ?? [];
            $basis_inv = $iR->repoInvLoadedquery($body['inv_id']);
            $basis_inv_id = $body['inv_id'];
            // Set the basis_inv to read-only;
            $basis_inv->setIs_read_only(true);
            $ajax_body = [
                'client_id'=>$body['client_id'],
                'group_id'=>$body['group_id'],
                'user_id'=>$body['user_id'],
                'creditinvoice_parent_id'=>$body['inv_id'],
                'status_id'=>$basis_inv->getStatus_id(),
                'is_read_only'=>false,
                'number'=>$gR->generate_invoice_number($body['group_id'], true),
                'discount_amount'=>null,
                'discount_percent'=>null,
                'url_key'=>'',
                'password'=>$body['password'], 
                'payment_method'=>0,
                'terms'=>'',
            ];
            // Save the basis invoice
            $iR->save($basis_inv);
            $ajax_content = new InvForm();
            $new_inv = new Inv();
            if ($ajax_content->load($ajax_body) && $validator->validate($ajax_content)->isValid()) {    
                $this->inv_service->saveInv($this->user_service->getUser(),$new_inv,$ajax_content, $this->sR);
                $this->inv_item_service->initializeCreditInvItems($basis_inv_id, $new_inv->getId(), $iiR,$iiaR, $this->sR);
                $this->inv_amount_service->initializeCreditInvAmount(new InvAmount(), $basis_inv_id, $new_inv->getId() );
                $this->inv_tax_rate_service->initializeCreditInvTaxRate($basis_inv_id, $new_inv->getId());
                $parameters = ['success'=>1];
               //return response to inv.js to reload page at location
                return $this->factory->createResponse(Json::encode($parameters));          
            } else {
                $parameters = [
                   'success'=>0,
                ];
                //return response to inv.js to reload page at location
                return $this->factory->createResponse(Json::encode($parameters));          
            }
    }
    
    public function default_taxes($inv, $trR, $validator){
        if ($trR->repoCountAll() > 0) {
            $taxrates = $trR->findAllPreloaded();
            foreach ($taxrates as $taxrate) {                
                $taxrate->getTax_rate_default()  == 1 ? $this->default_tax_inv($taxrate, $inv, $validator) : '';
            }
        }        
    }
    
    public function default_tax_inv($taxrate, $inv, $validator) : void {
        $inv_tax_rate_form = new InvTaxRateForm();
        $inv_tax_rate = [];
        $inv_tax_rate['inv_id'] = $inv->getId();
        $inv_tax_rate['tax_rate_id'] = $taxrate->getTax_rate_id();
        $inv_tax_rate['include_item_tax'] = 0;
        $inv_tax_rate['inv_tax_rate_amount'] = 0;
        ($inv_tax_rate_form->load($inv_tax_rate) && $validator->validate($inv_tax_rate_form)->isValid()) ? 
        $this->inv_tax_rate_service->saveInvTaxRate(new InvTaxRate(), $inv_tax_rate_form) : '';        
    }
    
    public function delete(CurrentRoute $currentRoute, InvRepository $invRepo, 
                           ICR $icR, InvCustomService $icS, IIR $iiR, InvItemService $iiS, ITRR $itrR,
                           InvTaxRateService $itrS, IAR $iaR, InvAmountService $iaS): Response {
        $this->rbac();
        try {
            $this->inv_service->deleteInv($this->inv($currentRoute, $invRepo), $icR, $icS, $iiR, $iiS, $itrR, $itrS, $iaR, $iaS); 
            $this->flash('info', 'Deleted.');
            return $this->web_service->getRedirectResponse('inv/index'); 
	} catch (Exception $e) {
            unset($e);
            $this->flash('danger', 'Cannot delete.');
            return $this->web_service->getRedirectResponse('inv/index'); 
        }
    }
    
    public function delete_inv_item(CurrentRoute $currentRoute, IIR $iiR ) : Response {
        $this->rbac();
        try {            
            $this->inv_item_service->deleteInvItem($this->inv_item($currentRoute,$iiR));
        } catch (Exception $e) {
            unset($e);
            $this->flash('danger', 'Cannot delete.');
        }
        $inv_id = $this->session->get('inv_id');
        return $this->factory->createResponse($this->viewRenderer->renderPartialAsString('/invoice/setting/inv_successful',
        ['heading'=>$this->sR->trans('invoice_items'),'message'=>$this->sR->trans('record_successfully_deleted'),'url'=>'inv/view','id'=>$inv_id]));  
    }
    
    public function delete_inv_tax_rate(CurrentRoute $currentRoute, ITRR $invtaxrateRepository) : Response {
        $this->rbac();
        try {            
            $this->inv_tax_rate_service->deleteInvTaxRate($this->invtaxrate($currentRoute,$invtaxrateRepository));
        } catch (Exception $e) {
            unset($e);
            $this->flash('danger', 'Cannot delete.');
        }
        $inv_id = $this->session->get('inv_id');
        return $this->factory->createResponse($this->viewRenderer->renderPartialAsString('/invoice/setting/inv_successful',
        ['heading'=>$this->sR->trans('invoice_tax_rate'),'message'=>$this->sR->trans('record_successfully_deleted'),'url'=>'inv/view','id'=>$inv_id]));  
    }
    
    public function download(CurrentRoute $currentRoute) : void
    {
        $aliases = $this->sR->get_invoice_archived_folder_aliases();
        $invoice = $currentRoute->getArgument('invoice');        
        header('Content-type: application/pdf');
        header('Content-Disposition: attachment; filename="' . urldecode($invoice) . '"');
        readfile($aliases->get('@archive_invoice'). DIRECTORY_SEPARATOR.urldecode($invoice));
    }
    
    public function edit(ViewRenderer $head, Request $request, CurrentRoute $currentRoute,
                        ValidatorInterface $validator,
                        IR $invRepo,
                        CR $clientRepo,
                        GR $groupRepo,
                        UR $userRepo,
                        IAR $iaR,
                        CFR $cfR,
                        CVR $cvR,
                        ICR $icR
    ): Response {
        $this->rbac();        
        $inv_id = $this->inv($currentRoute, $invRepo, true)->getId();
        $action = ['inv/edit', ['id' => $inv_id]];
        $parameters = [
            'title' => '',
            'action' => $action,
            'errors' => [],
            'body' => $this->body($this->inv($currentRoute, $invRepo, true)),
            'head'=>$head,
            's'=>$this->sR,
            'clients'=>$clientRepo->findAllPreloaded(),
            'groups'=>$groupRepo->findAllPreloaded(),
            'users'=>$userRepo->findAll(),
            'numberhelper' => $this->number_helper,
            'invs'=> $invRepo->findAllPreloaded(),
            'inv_statuses'=> $this->sR->getStatuses(),
            'cvH'=> new CVH($this->sR, new DateHelper($this->sR)),
            'custom_fields'=>$cfR->repoTablequery('inv_custom'),
            // Applicable to normally building up permanent selection lists eg. dropdowns
            'custom_values'=>$cvR->attach_hard_coded_custom_field_values_to_custom_field($cfR->repoTablequery('inv_custom')),
            // There will initially be no custom_values attached to this invoice until they are filled in the field on the form
            'inv_custom_values' => $this->inv_custom_values($inv_id, $icR),
        ];
        if ($request->getMethod() === Method::POST) {   
            $edited_body = $request->getParsedBody();
            $returned_form = $this->edit_save_form_fields($edited_body, $currentRoute, $validator, $invRepo, $groupRepo, $iaR);
            $parameters['body'] = $edited_body;
            $parameters['errors']=$returned_form->getFormErrors();
            $this->edit_save_custom_fields($edited_body, $validator, $icR, $inv_id);            
            return $this->factory->createResponse($this->view_renderer->renderPartialAsString('/invoice/setting/inv_successful',
            ['heading'=>'','message'=>$this->sR->trans('record_successfully_updated'),'url'=>'inv/view','id'=>$inv_id]));  
        }
        return $this->view_renderer->render('_form', $parameters);
    }
    
     public function edit_save_form_fields($edited_body, $currentRoute, $validator, $invRepo, $groupRepo, $iaR) : InvForm {
        $form = new InvForm();
        if ($form->load($edited_body) && $validator->validate($form)->isValid()) {
                $this->inv_service->saveInv($this->user_service->getUser(),$this->inv($currentRoute, $invRepo, true),$form,$this->sR, $groupRepo, $iaR);
        }
        return $form;
    }
    
    public function edit_save_custom_fields($parse, $validator, $icR, $inv_id) {
        $custom = $parse['custom'];
        foreach ($custom as $custom_field_id => $value) {
            $inv_custom = $icR->repoFormValuequery((string)$inv_id, (string)$custom_field_id);
            $inv_custom_input = [
                'inv_id'=>(int)$inv_id,
                'custom_field_id'=>(int)$custom_field_id,
                'value'=>(string)$value
            ];
            $form = new InvCustomForm();
            if ($form->load($inv_custom_input) && $validator->validate($form)->isValid())
            {
                $this->inv_custom_service->saveInvCustom($inv_custom, $form);     
            }
        }
    }
    
     //$this->flash
    private function flash($level, $message){
        $flash = new Flash($this->session);
        $flash->set($level, $message); 
        return $flash;
    }
    
    public function index(IAR $iaR, IR $invRepo, IRR $irR, CR $clientRepo, GR $groupRepo, CurrentRoute $currentRoute, sR $sR): Response
    {
        $pageNum = (int)$currentRoute->getArgument('page', '1');
        //status 0 => 'all';
        $status = (int)$currentRoute->getArgument('status', '0');
        $paginator = (new OffsetPaginator($this->invs($invRepo, $status)))
        ->withPageSize((int)$sR->setting('default_list_limit'))
        ->withCurrentPage($pageNum);       
        $canEdit = $this->rbac();        
        $parameters = [              
              'paginator' => $paginator,
              's'=> $this->sR,
              'flash'=>$this->flash('', ''),
              'canEdit' => $canEdit,
              'alert'=>$this->view_renderer->renderPartialAsString('/invoice/layout/alert',[
                    'flash'=>$this->flash('', ''),
                    'errors' => [],
              ]),
              'client_count'=>$clientRepo->count(),
              'invs' => $this->invs($invRepo, $status),              
              'inv_statuses'=> $this->sR->getStatuses(),
              'status'=> $status,
              'iaR'=>$iaR,
              'irR'=>$irR,
              'max'=>(int)$sR->setting('default_list_limit'),
              'modal_create_inv'=>$this->view_renderer->renderPartialAsString('modal_create_inv',[
                    'clients'=>$clientRepo->findAllPreloaded(),
                    's'=>$this->sR,
                    'invoice_groups'=>$groupRepo->findAllPreloaded(),
                    'datehelper'=> $this->date_helper,
               ])
        ];  
        return $this->view_renderer->render('index', $parameters);  
    }
    
    private function isAjaxRequest(Request $request): bool
    {
        return $request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
    }
    
    public function  items(string $items, ValidatorInterface $validator, $inv_id, int $order ,
                                     PR $pR, IIR $iir, IIAR $iiar, TRR $trr, UNR $unR) 
                                     : void {       
        foreach (Json::decode($items) as $item) {
            if ($item['item_name'] && (empty($item['item_id'])||!isset($item['item_id']))) {
                $ajax_content = new InvItemForm();
                $invitem = [];
                $invitem['name'] = $item['item_name'];
                $invitem['inv_id']=$item['inv_id'];
                $invitem['tax_rate_id']=$item['item_tax_rate_id'];
                $invitem['product_id']=($item['item_product_id']);
                //product_id used later to get description and name of product.
                $invitem['date_added']=new DateTimeImmutable();
                $invitem['quantity']=($item['item_quantity'] ? $this->number_helper->standardize_amount($item['item_quantity']) : floatval(0));
                $invitem['price']=($item['item_price'] ? $this->number_helper->standardize_amount($item['item_price']) : floatval(0));
                $invitem['discount_amount']= ($item['item_discount_amount']) ? $this->number_helper->standardize_amount($item['item_discount_amount']) : floatval(0);
                $invitem['order']= $order;
                $invitem['product_unit']=$unR->singular_or_plural_name($item['item_product_unit_id'],$item['item_quantity']);
                $invitem['product_unit_id']= ($item['item_product_unit_id'] ? $item['item_product_unit_id'] : null);                
                unset($item['item_id']);
                ($ajax_content->load($invitem) && $validator->validate($ajax_content)->isValid()) ? 
                $this->inv_item_service->saveInvItem(new InvItem(), $ajax_content, $inv_id, $pR, $trr, new InvItemAmountService($iiar),$iiar) : false;                 
                $order++;      
            }
            // Evaluate current items
            if ($item['item_name'] && (!empty($item['item_id'])||isset($item['item_id']))) {
                $unedited = $iir->repoInvItemquery($item['item_id']);  
                $ajax_content = new InvItemForm();
                $invitem = [];
                $invitem['name'] = $item['item_name'];
                $invitem['inv_id']=$item['inv_id'];
                $invitem['tax_rate_id']=$item['item_tax_rate_id'] ? $item['item_tax_rate_id'] : null;
                $invitem['product_id']=($item['item_product_id'] ? $item['item_product_id'] : null);
                //product_id used later to get description and name of product.
                $invitem['date_added']=new DateTimeImmutable();
                $invitem['quantity']=($item['item_quantity'] ? $this->number_helper->standardize_amount($item['item_quantity']) : floatval(0));
                $invitem['price']=($item['item_price'] ? $this->number_helper->standardize_amount($item['item_price']) : floatval(0));
                $invitem['discount_amount']= ($item['item_discount_amount']) ? $this->number_helper->standardize_amount($item['item_discount_amount']) : floatval(0);
                $invitem['order']= $order;
                $invitem['product_unit']=$unR->singular_or_plural_name($item['item_product_unit_id'],$item['item_quantity']);
                $invitem['product_unit_id']= ($item['item_product_unit_id'] ? $item['item_product_unit_id'] : null);                
                unset($item['item_id']);
                ($ajax_content->load($invitem) && $validator->validate($ajax_content)->isValid()) ? 
                $this->inv_item_service->saveInvItem($unedited, $ajax_content, $inv_id, $pR, $trr, new IIAS($iiar),$iiar) : false;             
            }      
        }
    }    
    
    // Called from inv.js inv_to_pdf_confirm_with_custom_fields
    public function pdf(CurrentRoute $currentRoute, CR $cR, CVR $cvR, CFR $cfR, IAR $iaR, ICR $icR, IIR $iiR, IIAR $iiaR, IR $iR, ITRR $itrR, SR $sR, UIR $uiR, Request $request) {
        // include is a value of 0 or 1 passed from inv.js function inv_to_pdf_with(out)_custom_fields indicating whether the user
        // wants custom fields included on the inv or not.
        $include = $currentRoute->getArgument('include');        
        $inv_id = $this->session->get('inv_id');
        $inv_amount = (($iaR->repoInvAmountCount($inv_id) > 0) ? $iaR->repoInvquery($inv_id) : null);
        $custom = (($include===(string)1) ? true : false);
        $inv_custom_values = $this->inv_custom_values($this->session->get('inv_id'),$icR);
        // session is passed to the pdfHelper and will be used for the locale ie. $session->get('_language') or the print_language ie $session->get('print_language')
        $pdfhelper = new PdfHelper($sR, $this->session);
        // The invoice will be streamed ie. shown, and not archived
        $stream = true;
        // If we are required to mark invoices as 'sent' when sent.
        if ($sR->setting('mark_invoices_sent_pdf') == 1) {
            $this->generate_inv_number_if_applicable($inv_id, $iR, $sR);
            $sR->invoice_mark_sent($inv_id, $iR);
        }
        $inv = $iR->repoInvUnloadedquery((string)$inv_id);        
        $pdfhelper->generate_inv_pdf($inv_id, $inv->getUser_id(), $stream, $custom, $inv_amount, $inv_custom_values, $cR, $cvR, $cfR, $iiR, $iiaR, $iR, $itrR, $uiR, $this->view_renderer);        
    }
    
    public function generate_inv_number_if_applicable($inv_id, $iR, $sR) : void
    {
        $inv = $iR->repoInvUnloadedquery($inv_id);
        if ($iR->repoCount($inv_id)>0) {
            if ($inv->getStatus_id() === 1 && $inv->getNumber() === "") {
                // Generate new inv number if applicable
                if ($sR->get_setting('generate_invoice_number_for_draft') === 0) {
                    $inv_number = $iR->get_inv_number($inv->getGroup_id());
                    // Set new invoice number and save
                    $inv->setNumber($inv_number);
                    $iR->save($inv);
                }
            }
        }
    }
        
    private function inv(CurrentRoute $currentRoute,InvRepository $invRepo, $unloaded = false) 
    {
        $id = $currentRoute->getArgument('id');
        $inv = ($unloaded ? $invRepo->repoInvUnLoadedquery($id) : $invRepo->repoInvLoadedquery($id));
        if ($inv === null) {
            return $this->web_service->getNotFoundResponse();
        }
        return $inv;
    }
    
    private function invs(InvRepository $invRepo, $status) 
    {
        $invs = $invRepo->findAllWithStatus($status);    
        if ($invs === null) {
            return $this->web_service->getNotFoundResponse();
        }
        return $invs;
    }
    
    public function inv_custom_values($inv_id, icR $icR) : array
    {
        // Get all the custom fields that have been registered with this inv on creation, retrieve existing values via repo, and populate 
        // custom_field_form_values array
        $custom_field_form_values = [];
        if ($icR->repoInvCount($inv_id) > 0) {
            $inv_custom_fields = $icR->repoFields($inv_id);
            foreach ($inv_custom_fields as $key => $val) {
                $custom_field_form_values['custom[' . $key . ']'] = $val;
            }
        }
        return $custom_field_form_values;
    }
    
    private function inv_item(CurrentRoute $currentRoute,IIR $invitemRepository) 
    {
        $id = $currentRoute->getArgument('id');       
        $invitem = $invitemRepository->repoInvItemquery($id);
        if ($invitem === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $invitem;
    } 
    
     
    private function modal_add_payment(ViewRenderer $head, Request $request, ICR $icR, CFR $cfR, CVR $cvR){
        $data_inv_js = $request->getQueryParams() ?? [];
        $amount = $data_inv_js['amount'];
        $payment_method_id = $data_inv_js['payment_method_id'];
        $date = $data_inv_js['date']; 
        $note = $data_inv_js['note'];
        $parameters = [
                's'=>$this->sR,
                'action' => ['customfield/index'],
                'urlGenerator'=>$this->url_generator,
                'inv_id'=>$this->session->get('inv_id'),
                'datehelper'=>$this->date_helper,
                // Get all the fields that have been setup for a standard invoice in custom_fields and and retrieve values in inv_custom table
                'fields' => $icR->repoFields($this->session->get('inv_id')),
                // Get the standard extra custom fields built for every invoice. 
                'custom_fields'=>$cfR->repoTablequery('inv_custom'),
                'custom_values'=>$cvR->attach_hard_coded_custom_field_values_to_custom_field($cfR->repoTablequery('inv_custom')),
                // Use the cvh to build the fields
                'cvH'=> new CVH($this->sR),
                'inv_custom_values' => $this->inv_custom_values($this->session->get('inv_id'),$icR),
                // Use the head value to generate a save button
                'head'=>$head
        ];
        return $this->view_renderer->render('_inv_custom_fields', $parameters);
        
    }
    
    private function inv_to_inv_inv_amount($inv_id,$copy_id) {
        $this->inv_amount_service->initializeCopyInvAmount(new InvAmount(), $inv_id, $copy_id);
    }
    
    // Data fed from inv.js->$(document).on('click', '#inv_to_inv_confirm', function () {
    public function inv_to_inv_confirm(Request $request, ValidatorInterface $validator, 
                                           GR $gR, IIAS $iiaS, PR $pR, IAR $iaR, ICR $icR,
                                           IIAR $iiaR, IIR $iiR,IR $iR, ITRR $itrR, TRR $trR, UNR $unR) : Response
    {
        $this->rbac();  
        $data_inv_js = $request->getQueryParams() ?? [];
        $inv_id = (string)$data_inv_js['inv_id'];
        $original = $iR->repoInvUnloadedquery($inv_id);
        $group_id = $original->getGroup_id();
        $ajax_body = [
                'quote_id'=>null,
                'client_id'=>$data_inv_js['client_id'],
                'group_id'=>$group_id,
                'status_id'=>1,
                'number'=>$gR->generate_invoice_number((string)$group_id),
                'creditinvoice_parent_id'=>null ,
                'discount_amount'=>floatval($original->getDiscount_amount()),
                'discount_percent'=>floatval($original->getDiscount_percent()),
                'url_key'=>'',
                'password'=>'',
                'payment_method'=>'',
                'terms'=>'',
        ];
        $form = new InvForm();
        $copy = new Inv();
        if (($form->load($ajax_body) && $validator->validate($form)->isValid())) {    
            $this->inv_service->saveInv($this->user_service->getUser(), $copy, $form, $this->sR, $gR, $iaR);
            // Transfer each inv_item to inv_item and the corresponding inv_item_amount to inv_item_amount for each item
            $copy_id = $copy->getId();
            $this->inv_to_inv_inv_items($inv_id,$copy_id, $iiaR, $iiaS, $pR, $iiR, $trR,$validator, $unR);
            $this->inv_to_inv_inv_tax_rates($inv_id,$copy_id,$itrR, $validator);
            $this->inv_to_inv_inv_custom($inv_id,$copy_id,$icR, $validator);
            $this->inv_to_inv_inv_amount($inv_id,$copy_id);            
            $iR->save($copy);
            $parameters = ['success'=>1];
            //return response to inv.js to reload page at location
            return $this->factory->createResponse(Json::encode($parameters));          
        } else {
            $parameters = [
               'success'=>0,
            ];
            //return response to inv.js to reload page at location
            return $this->factory->createResponse(Json::encode($parameters));          
        } 
    }
    
    
    private function inv_to_inv_inv_custom($inv_id, $copy_id, $icR, $validator) {
        $inv_customs = $icR->repoFields($inv_id);
        foreach ($inv_customs as $inv_custom) {
            $copy_custom = [
                'inv_id'=>$copy_id,
                'custom_field_id'=>$inv_custom->getCustom_field_id(),
                'value'=>$inv_custom->getValue(),
            ];
            $entity = new InvCustom();
            $form = new InvCustomForm();
            if ($form->load($copy_custom) && $validator->validate($form)->isValid()) {    
                $this->inv_custom_service->saveInvCustom($entity,$form);            
            }
        }        
    }
    
    private function inv_to_inv_inv_items($inv_id, $copy_id, $iiaR, $iiaS, $pR, $iiR, $trR, $validator, $unR) {
        // Get all items that belong to the original invoice
        $items = $iiR->repoInvItemIdquery((string)$inv_id);
        foreach ($items as $inv_item) {
            $copy_item = [
                'inv_id'=>$copy_id,
                'tax_rate_id'=>$inv_item->getTax_rate_id(),
                'product_id'=>$inv_item->getProduct_id(),
                'task_id'=>'',
                'name'=>$inv_item->getName(),
                'description'=>$inv_item->getDescription(),
                'quantity'=>$inv_item->getQuantity(),
                'price'=>$inv_item->getPrice(),
                'discount_amount'=>$inv_item->getDiscount_amount(),
                'order'=>$inv_item->getOrder(),
                'is_recurring'=>$inv_item->getIs_recurring(),
                'product_unit'=>$inv_item->getProduct_unit(),
                'product_unit_id'=>$inv_item->getProduct_unit_id(),
                // Recurring date
                'date'=>''
            ];
            // Create an equivalent invoice item for the invoice item
            $copyitem = new InvItem();
            $form = new InvItemForm();
            if ($form->load($copy_item) && $validator->validate($form)->isValid()) {
                $this->inv_item_service->saveInvItem($copyitem, $form, $copy_id, $pR, $trR , $iiaS, $iiaR, $unR);
            }
        }
    }
    
    private function inv_to_inv_inv_tax_rates($inv_id, $copy_id, $itrR, $validator) {
        // Get all tax rates that have been setup for the invoice
        $inv_tax_rates = $itrR->repoInvquery($inv_id);        
        foreach ($inv_tax_rates as $inv_tax_rate){            
            $copy_tax_rate = [
                'inv_id'=>$copy_id,
                'tax_rate_id'=>$inv_tax_rate->getTax_rate_id(),
                'include_item_tax'=>$inv_tax_rate->getInclude_item_tax(),
                'amount'=>$inv_tax_rate->getInv_tax_rate_amount(),
            ];
            $entity = new InvTaxRate();
            $form = new InvTaxRateForm();
            if ($form->load($copy_tax_rate) && $validator->validate($form)->isValid()) {    
                $this->inv_tax_rate_service->saveInvTaxRate($entity,$form);
            }
        }        
    }    
    
    private function invtaxrate(CurrentRoute $currentRoute, ITRR $invtaxrateRepository) 
    {
        $id = $currentRoute->getArgument('id');       
        $invtaxrate = $invtaxrateRepository->repoInvTaxRatequery($id);
        if ($invtaxrate === null) {
            return $this->web_service->getNotFoundResponse();
        }
        return $invtaxrate;
    }
    
    private function rbac() 
    {
        $canEdit = $this->user_service->hasPermission('editInv');
        if (!$canEdit){
            $this->flash('warning', $this->translator->translate('invoice.permission'));
            return $this->web_service->getRedirectResponse('inv/index');
        }
        return $canEdit;
    }    
    
    // inv/view => '#btn_save_inv_custom_fields' => inv_custom_field.js => /invoice/inv/save_custom";
    public function save_custom(ValidatorInterface $validator, Request $request, ICR $icR) : Response
    {
            $parameters['success'] = 0;
            $this->rbac();       
            $js_data = $request->getQueryParams() ?? [];        
            $inv_id = $js_data['inv_id'];
            $custom_field_body = [            
                'custom'=>$js_data['custom'] ?: '',            
            ];
            $this->save_custom_fields($validator, $custom_field_body,$inv_id, $icR);
            $parameters =[
                'success'=>1,
            ];
            return $this->factory->createResponse(Json::encode($parameters)); 
    }
    
    public function save_custom_fields(ValidatorInterface $validator, $array, $inv_id, $icR) : void
    {   
        if (!empty($array['custom'])) {
            $db_array = [];
            $values = [];
            foreach ($array['custom'] as $custom) {
                if (preg_match("/^(.*)\[\]$/i", $custom['name'], $matches)) {
                    $values[$matches[1]][] = $custom['value'] ;
                } else {
                    $values[$custom['name']] = $custom['value'];
                }
            }            
            foreach ($values as $key => $value) {                
                preg_match("/^custom\[(.*?)\](?:\[\]|)$/", $key, $matches);
                if ($matches) {
                    $key_value = preg_match('/\d+/', $key, $m) ? $m[0] : '';
                    $db_array[$key_value] = $value;
                }
            }            
            foreach ($db_array as $key => $value){
               if ($value !=='') { 
                $ajax_custom = new InvCustomForm();
                $inv_custom = [];
                $inv_custom['inv_id']=$inv_id;
                $inv_custom['custom_field_id']=$key;
                $inv_custom['value']=$value; 
                $model = ($icR->repoInvCustomCount($inv_id,(string)$key) == 1 ? $icR->repoFormValuequery($inv_id,(string) $key) : new InvCustom());
                ($ajax_custom->load($inv_custom) && $validator->validate($ajax_custom)->isValid()) ? 
                        $this->inv_custom_service->saveInvCustom($model, $ajax_custom) : '';                                   
               }
            }             
        } 
    }
    
    // '#inv_tax_submit' => inv.js 
    public function save_inv_tax_rate(Request $request, ValidatorInterface $validator) : Response {
        if ($this->isAjaxRequest($request)) {
            $this->rbac();  
            $body = $request->getQueryParams() ?? [];
            $ajax_body = [
                'inv_id'=>$body['inv_id'],
                'tax_rate_id'=>$body['inv_tax_rate_id'],
                'include_item_tax'=>$body['include_inv_item_tax'],
                'inv_tax_rate_amount'=>floatval(0.00),
            ];
            $ajax_content = new InvTaxRateForm();
            if ($ajax_content->load($ajax_body) && $validator->validate($ajax_content)->isValid()) {    
                $this->inv_tax_rate_service->saveInvTaxRate(new InvTaxRate(), $ajax_content);
                $parameters = [
                    'success'=>1,                    
                ];
                //return response to inv.js to reload page at location
                return $this->factory->createResponse(Json::encode($parameters));          
            } else {
                $parameters = [
                   'success'=>0
                 ];
                //return response to inv.js to reload page at location
                return $this->factory->createResponse(Json::encode($parameters));          
            }
        } 
    }
    
    public function store(
        Request $request,
        FilesystemInterface $filesystem
    ) {
        $file = $request->file('upload');
        $stream = fopen($file->getRealPath(), 'r+');
        $filesystem->writeStream(
            'uploads/'.$file->getClientOriginalName(),
            $stream
        );
        fclose($stream);
    }
    
    public function view(ViewRenderer $head, CurrentRoute $currentRoute, Request $request,
                         CFR $cfR, CVR $cvR, PR $pR, IAR $iaR, IIAR  $iiaR, IIR $iiR, IR $iR, IRR $irR, ITRR $itrR,PMR $pmR, TRR $trR, FR $fR,  UNR $uR, CR $cR, GR $gR, ICR $icR, PYMR $pymR)
                         : Response {
        $this->rbac();
        $this->session->set('inv_id',$this->inv($currentRoute, $iR, false)->getId());
        $this->number_helper->calculate_inv($this->session->get('inv_id'), $iiR, $iiaR, $itrR, $iaR, $iR, $pymR); 
        $inv_tax_rates = (($itrR->repoCount($this->session->get('inv_id')) > 0) ? $itrR->repoInvquery($this->inv($currentRoute, $iR,false)->getId()) : null); 
        $inv_amount = (($iaR->repoInvAmountCount($this->inv($currentRoute, $iR)->getId()) > 0) ? $iaR->repoInvquery($this->session->get('inv_id')) : null);
        $inv_custom_values = $this->inv_custom_values($this->session->get('inv_id'),$icR);
        $parameters = [
            'title' => $this->sR->trans('view'),
            'body' => $this->body($this->inv($currentRoute, $iR, false)),
            'datehelper'=> $this->date_helper,
            's'=>$this->sR,
            'alert'=>$this->view_renderer->renderPartialAsString('/invoice/layout/alert',[
                    'flash'=>$this->flash('', ''),
                    'errors' => [],
            ]),
            'iaR'=>$iaR,
            'payment_methods'=>$pmR->findAllPreloaded(),
            // If a custom field exists for payments, use it/them on the payment form. 
            'payment_cf_exist' => $cfR->repoTableCountquery('payment_custom') > 0 ? true : false,
            'add_inv_item'=>$this->view_renderer->renderPartialAsString('/invoice/invitem/_item_form',[
                    'action' => ['invitem/add'],
                    'errors' => [],
                    'body' => $request->getParsedBody(),
                    's'=>$this->sR,
                    'head'=>$head,
                    'inv'=>$iR->repoInvLoadedquery($this->session->get('inv_id')),
                    'is_recurring'=>($irR->repoCount($this->session->get('inv_id')) > 0 ? 'true' : 'false'),
                    'inv_id'=>$this->session->get('inv_id'),
                    'tax_rates'=>$trR->findAllPreloaded(),
                    'products'=>$pR->findAllPreloaded(),
                    'units'=>$uR->findAllPreloaded(),
                    'numberhelper'=>$this->number_helper
            ]), 
            // Get all the fields that have been setup for this SPECIFIC invoice in inv_custom. 
            'fields' => $icR->repoFields($this->session->get('inv_id')),
            // Get the standard extra custom fields built for EVERY invoice. 
            'custom_fields'=>$cfR->repoTablequery('inv_custom'),
            'custom_values'=>$cvR->attach_hard_coded_custom_field_values_to_custom_field($cfR->repoTablequery('inv_custom')),
            'cvH'=> new CVH($this->sR),
            'inv_custom_values' => $inv_custom_values,
            'inv_statuses'=> $this->sR->getStatuses(),  
            'inv'=>$iR->repoInvLoadedquery($this->session->get('inv_id')),   
            'partial_item_table'=>$this->view_renderer->renderPartialAsString('/invoice/inv/partial_item_table',[
                'numberhelper'=> $this->number_helper,          
                'products'=>$pR->findAllPreloaded(),
                'inv_items'=>$iiR->repoInvquery($this->session->get('inv_id')),
                'inv_item_amount'=>$iiaR,
                'inv_tax_rates'=>$inv_tax_rates,
                'inv_amount'=> $inv_amount,
                'inv'=>$iR->repoInvLoadedquery($this->session->get('inv_id')),  
                's'=>$this->sR,
                'tax_rates'=>$trR->findAllPreloaded(),
                'units'=>$uR->findAllPreloaded(),
            ]),
            'modal_choose_items'=>$this->view_renderer->renderPartialAsString('/invoice/product/modal_product_lookups_inv',
            [
                's'=>$this->sR,
                'families'=>$fR->findAllPreloaded(),
                'default_item_tax_rate'=> $this->sR->get_setting('default_item_tax_rate') !== '' ?: 0,
                'filter_product'=> '',            
                'filter_family'=> '',
                'reset_table'=> '',
                'products'=>$pR->findAllPreloaded(),
                'head'=> $head,
            ]),
            'modal_add_inv_tax'=>$this->view_renderer->renderPartialAsString('modal_add_inv_tax',['s'=>$this->sR,'tax_rates'=>$trR->findAllPreloaded()]),
            'modal_copy_inv'=>$this->view_renderer->renderPartialAsString('modal_copy_inv',[ 's'=>$this->sR,
                'inv'=>$iR->repoInvLoadedquery($this->session->get('inv_id')),
                'clients'=>$cR->findAllPreloaded(),                
                'groups'=>$gR->findAllPreloaded(),
            ]),
            'modal_delete_inv'=>$this->view_renderer->renderPartialAsString('modal_delete_inv',
                    ['action'=>['inv/delete', ['id' => $this->session->get('inv_id')]],
                    's'=>$this->sR,   
            ]),
            'modal_delete_items'=>$this->view_renderer->renderPartialAsString('/invoice/inv/modal_delete_item',[
                    'partial_item_table_modal'=>$this->view_renderer->renderPartialAsString('/invoice/invitem/_partial_item_table_modal',[
                        'invitems'=>$iiR->repoInvquery($this->session->get('inv_id')),
                        's'=>$this->sR,
                        'numberhelper'=>$this->number_helper,
                    ]),
                    's'=>$this->sR,
            ]),
            'modal_change_client'=>$this->view_renderer->renderPartialAsString('modal_change_client', [
                     'inv'=> $this->inv($currentRoute, $iR, true),   
                     's'=>$this->sR,
                     'clients'=> $cR->findAllPreloaded()
            ]),
            'modal_inv_to_pdf'=>$this->view_renderer->renderPartialAsString('modal_inv_to_pdf',[
                     's'=>$this->sR,
                     'inv'=> $this->inv($currentRoute, $iR, true),                        
            ]),
            'modal_create_recurring'=>$this->view_renderer->renderPartialAsString('modal_create_recurring',[
                     's'=>$this->sR,
                     'recur_frequencies'=>$irR->recur_frequencies(), 
                     'datehelper'=>$this->date_helper
            ]),
            'modal_create_credit'=>$this->view_renderer->renderPartialAsString('modal_create_credit',[
                     's'=>$this->sR,
                     'invoice_groups'=>$gR->findAllPreloaded(),
                     'inv'=>$this->inv($currentRoute, $iR, false),
                     'datehelper'=>$this->date_helper
            ]),
            'dropzone_inv_html'=>$this->view_renderer->renderPartialAsString('dropzone_inv_html',[
                     's'=>$this->sR,
            ]),
            'view_custom_fields'=>$this->view_renderer->renderPartialAsString('view_custom_fields', [
                     'custom_fields'=>$cfR->repoTablequery('inv_custom'),
                     'custom_values'=>$cvR->attach_hard_coded_custom_field_values_to_custom_field($cfR->repoTablequery('inv_custom')),
                     'inv_custom_values'=> $inv_custom_values,  
                     'cvH'=> new CVH($this->sR),
                     's'=>$this->sR,   
            ]),        
        ];
        return $this->view_renderer->render('view', $parameters);
    }
}