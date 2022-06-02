<?php 
declare(strict_types=1); 

namespace App\Invoice\Quote;
// Entity's
use App\Invoice\Entity\Inv;
use App\Invoice\Entity\InvItem;
use App\Invoice\Entity\InvAmount;
use App\Invoice\Entity\InvCustom;
use App\Invoice\Entity\InvTaxRate;
use App\Invoice\Entity\Quote;
use App\Invoice\Entity\QuoteAmount;
use App\Invoice\Entity\QuoteItem;
use App\Invoice\Entity\QuoteCustom;
use App\Invoice\Entity\QuoteTaxRate;
// Services
// Inv
use App\User\UserService;
use App\Invoice\Inv\InvService;
use App\Invoice\InvItem\InvItemService;
use App\Invoice\InvAmount\InvAmountService;
use App\Invoice\InvItemAmount\InvItemAmountService;
use App\Invoice\InvTaxRate\InvTaxRateService;
use App\Invoice\InvCustom\InvCustomService;
// Quote
use App\Invoice\Quote\QuoteService;
use App\Invoice\QuoteAmount\QuoteAmountService;
use App\Invoice\QuoteCustom\QuoteCustomService;
use App\Invoice\QuoteItem\QuoteItemService;
use App\Invoice\QuoteItemAmount\QuoteItemAmountService as QIAS;
use App\Invoice\QuoteTaxRate\QuoteTaxRateService;
use App\Service\WebControllerService;
// Forms
use App\Invoice\Inv\InvForm;
use App\Invoice\InvAmount\InvAmountForm;
use App\Invoice\InvItem\InvItemForm;
use App\Invoice\InvCustom\InvCustomForm;
use App\Invoice\InvTaxRate\InvTaxRateForm;
use App\Invoice\QuoteItem\QuoteItemForm;
use App\Invoice\QuoteTaxRate\QuoteTaxRateForm;
use App\Invoice\QuoteCustom\QuoteCustomForm;
use App\Invoice\Quote\QuoteForm;
// Repositories
use App\Invoice\Client\ClientRepository as CR;
use App\Invoice\CustomValue\CustomValueRepository as CVR;
use App\Invoice\CustomField\CustomFieldRepository as CFR;
use App\Invoice\Family\FamilyRepository as FR;
use App\Invoice\Group\GroupRepository as GR;
use App\Invoice\Inv\InvRepository as IR;
use App\Invoice\InvItemAmount\InvItemAmountRepository as IIAR;
use App\Invoice\Product\ProductRepository as PR;
use App\Invoice\Quote\QuoteRepository as QR;
use App\Invoice\QuoteAmount\QuoteAmountRepository as QAR;
use App\Invoice\QuoteCustom\QuoteCustomRepository as QCR;
use App\Invoice\QuoteItemAmount\QuoteItemAmountRepository as QIAR;
use App\Invoice\QuoteItem\QuoteItemRepository as QIR;
use App\Invoice\QuoteTaxRate\QuoteTaxRateRepository as QTRR;
use App\Invoice\Setting\SettingRepository as SR;
use App\Invoice\TaxRate\TaxRateRepository as TRR;
use App\Invoice\Unit\UnitRepository as UNR;
use App\Invoice\UserInv\UserInvRepository as UIR;
use App\User\UserRepository as UR;
// App Helpers
Use App\Invoice\Helpers\DateHelper;
use App\Invoice\Helpers\PdfHelper;
use App\Invoice\Helpers\NumberHelper;
use App\Invoice\Helpers\ModalHelper;
use App\Invoice\Helpers\CustomValuesHelper as CVH;
// Yii
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\DataResponse\DataResponseFactoryInterface;
use Yiisoft\Html\Html;
use Yiisoft\Http\Method;
use Yiisoft\Json\Json;
use Yiisoft\Router\FastRoute\UrlGenerator;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Session\Flash\Flash;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\View\ViewRenderer;
use Yiisoft\Translator\TranslatorInterface as Translator; 
// Psr\Http
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
// Miscellaneous
use \Exception;
use \DateTimeImmutable;

final class QuoteController
{
    private DataResponseFactoryInterface $factory;
    private NumberHelper $number_helper; 
    private InvAmountService $inv_amount_service;
    private InvService $inv_service;
    private InvCustomService $inv_custom_service;
    private InvItemService $inv_item_service;
    private InvItemAmountService $inv_item_amount_service;
    private InvTaxRateService $inv_tax_rate_service;
    private QuoteAmountService $quote_amount_service;
    private QuoteCustomService $quote_custom_service;
    private QuoteItemService $quote_item_service;    
    private QuoteService $quote_service;
    private QuoteTaxRateService $quote_tax_rate_service;
    private SessionInterface $session;
    private Translator $translator;
    private SR $sR;
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
        InvItemAmountService $inv_item_amount_service,
        InvTaxRateService $inv_tax_rate_service,
        QuoteAmountService $quote_amount_service,
        QuoteCustomService $quote_custom_service,    
        QuoteItemService $quote_item_service,    
        QuoteService $quote_service,
        QuoteTaxRateService $quote_tax_rate_service,
        SessionInterface $session,
        SR $sR,
        Translator $translator,
        UserService $user_service,        
        UrlGenerator $url_generator,
        ViewRenderer $view_renderer,
        WebControllerService $web_service,                        
    )    
    {
        $this->factory = $factory;
        $this->inv_amount_service = $inv_amount_service;
        $this->inv_service = $inv_service;
        $this->inv_custom_service = $inv_custom_service;
        $this->inv_item_service = $inv_item_service;
        $this->inv_item_amount_service = $inv_item_amount_service;
        $this->inv_tax_rate_service = $inv_tax_rate_service;
        $this->number_helper = new NumberHelper($sR);
        $this->modal_helper = new ModalHelper($sR);
        $this->quote_amount_service = $quote_amount_service;
        $this->quote_custom_service = $quote_custom_service;
        $this->quote_item_service = $quote_item_service;        
        $this->quote_service = $quote_service;
        $this->quote_tax_rate_service = $quote_tax_rate_service;
        $this->session = $session;
        $this->sR = $sR;
        $this->translator = $translator;
        $this->user_service = $user_service;
        $this->url_generator = $url_generator;        
        $this->view_renderer = $view_renderer->withControllerName('invoice/quote')
                                             ->withLayout(dirname(dirname(__DIR__)) .'/Invoice/Layout/main.php');
        $this->web_service = $web_service;
    }
    
    public function add(ViewRenderer $head, Request $request, 
                        ValidatorInterface $validator,
                        IR $invRepo,
                        CR $clientRepo,
                        GR $groupRepo,
                        UR $userRepo,
                        QR $quoteRepo,
                        QAR $qaR,
    ) : Response
    {
        $this->rbac();
        $parameters = [
            'title' => 'Add',
            'action' => ['quote/add'],
            'alert'=>$this->view_renderer->renderPartialAsString('/invoice/layout/alert',[
                    'flash'=>$this->flash('', ''),
                    'errors' => [],
            ]),
            'errors' => [],
            'body' =>$request->getParsedBody(),
            's'=>$this->sR,
            'head'=>$head,
            'quote'=>$quoteRepo->findAllPreloaded(),
            'quote_statuses'=>$this->sR->getStatuses(),
            'invs'=>$invRepo->findAllPreloaded(),
            'clients'=>$clientRepo->findAllPreloaded(),
            'groups'=>$groupRepo->findAllPreloaded(),
            'users'=>$userRepo->findAll(),
            'numberhelper' => $this->number_helper,
        ];        
        if ($request->getMethod() === Method::POST) {            
            $form = new QuoteForm();
            $parameters['body']['number'] = $groupRepo->generate_invoice_number($parameters['body']['group_id'], true); 
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {                
                $this->quote_service->saveQuote($this->user_service->getUser(),new Quote(),$form,$this->sR,$groupRepo,$qaR);
                return $this->web_service->getRedirectResponse('quote/index');
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
    
    private function body($quote) {
        $body = [
          'number'=>$quote->getNumber(),
            
          'id'=>$quote->getId(),
          'inv_id'=>$quote->getInv_id(),
          'user_id'=>$quote->getUser_id(),
          
          'client_id'=>$quote->getClient_id(),          
         
          'date_created'=>$quote->getDate_created(),
          'date_modified'=>$quote->getDate_modified(),
          'date_expires'=>$quote->getDate_expires(),            
            
          'group_id'=>$quote->getGroup_id(),
          'status_id'=>$quote->getStatus_id(),  
          
          'discount_amount'=>$quote->getDiscount_amount(),
          'discount_percent'=>$quote->getDiscount_percent(),
          'url_key'=>$quote->getUrl_key(),
          'password'=>$quote->getPassword(),
          'notes'=>$quote->getNotes(),          
            
        ];
        return $body;
    }
    
    // Data fed from quote.js->$(document).on('click', '#quote_create_confirm', function () {
    public function create_confirm(Request $request, ValidatorInterface $validator, GR $gR, TRR $trR, QAR $qaR) : Response
    {
        $this->rbac();  
        $body = $request->getQueryParams() ?? [];
        $quote_number = $gR->generate_invoice_number($body['group_id'], true); 
        $ajax_body = [
            'inv_id'=>null,
            'client_id'=>$body['client_id'],
            'group_id'=>$body['group_id'],
            'status_id'=>1,
            'number'=>$quote_number,
            'discount_amount'=>floatval(0),
            'discount_percent'=>floatval(0),
            'url_key'=>'',
            'password'=>$body['quote_password'],              
            'notes'=>'',
        ];
        $ajax_content = new QuoteForm();
        $quote = new Quote();
        if ($ajax_content->load($ajax_body) && $validator->validate($ajax_content)->isValid()) {    
            $this->quote_service->saveQuote($this->user_service->getUser(),$quote,$ajax_content, $this->sR, $gR, $qaR);
            $this->quote_amount_service->initializeQuoteAmount(new QuoteAmount(), (int)$quote->getId());
            $this->default_taxes($quote, $trR, $validator);            
            $parameters = ['success'=>1];
           //return response to quote.js to reload page at location
            return $this->factory->createResponse(Json::encode($parameters));          
        } else {
            $parameters = [
               'success'=>0,
            ];
            //return response to quote.js to reload page at location
            return $this->factory->createResponse(Json::encode($parameters));          
        } 
    }
    
    public function custom_fields(ValidatorInterface $validator, $array, $quote_id, $qcR) : void
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
                    // Reduce eg.  customview[4] to 4 
                    $key_value = preg_match('/\d+/', $key, $m) ? $m[0] : '';
                    $db_array[$key_value] = $value;
                }
            }            
            foreach ($db_array as $key => $value){
               if ($value !=='') { 
                $ajax_custom = new QuoteCustomForm();
                $quote_custom = [];
                $quote_custom['quote_id']=$quote_id;
                $quote_custom['custom_field_id']=$key;
                $quote_custom['value']=$value; 
                $model = ($qcR->repoQuoteCustomCount($quote_id,(string)$key) > 0 ? $qcR->repoFormValuequery($quote_id,(string) $key) : new QuoteCustom());
                ($ajax_custom->load($quote_custom) && $validator->validate($ajax_custom)->isValid()) ? 
                        $this->quote_custom_service->saveQuoteCustom($model, $ajax_custom) : '';                                   
               }
            }             
        } 
    }
    
    public function default_taxes($quote, $trR, $validator){
        if ($trR->repoCountAll() > 0) {
            $taxrates = $trR->findAllPreloaded();
            foreach ($taxrates as $taxrate) {                
                $taxrate->getTax_rate_default()  == 1 ? $this->default_tax_quote($taxrate, $quote, $validator) : '';
            }
        }        
    }
    
    public function default_tax_quote($taxrate, $quote, $validator) : void {
        $quote_tax_rate_form = new QuoteTaxRateForm();
        $quote_tax_rate = [];
        $quote_tax_rate['quote_id'] = $quote->getId();
        $quote_tax_rate['tax_rate_id'] = $taxrate->getTax_rate_id();
        $quote_tax_rate['include_item_tax'] = 0;
        $quote_tax_rate['quote_tax_rate_amount'] = 0;
        ($quote_tax_rate_form->load($quote_tax_rate) && $validator->validate($quote_tax_rate_form)->isValid()) ? 
        $this->quote_tax_rate_service->saveQuoteTaxRate(new QuoteTaxRate(), $quote_tax_rate_form) : '';        
    }
    
        
    public function delete(CurrentRoute $currentRoute, QuoteRepository $quoteRepo, 
                           QCR $qcR, QuoteCustomService $qcS, QIR $qiR, QuoteItemService $qiS, QTRR $qtrR,
                           QuoteTaxRateService $qtrS, QAR $qaR, QuoteAmountService $qaS): Response {
        $this->rbac();
        try {
            $this->quote_service->deleteQuote($this->quote($currentRoute, $quoteRepo), $qcR, $qcS, $qiR, $qiS, $qtrR, $qtrS, $qaR, $qaS); 
            $this->flash('info','Deleted.');
            return $this->web_service->getRedirectResponse('quote/index'); 
	} catch (Exception $e) {
            unset($e);
            $this->flash('danger', 'Cannot delete.');
            return $this->web_service->getRedirectResponse('quote/index'); 
        }
    }
    
    public function delete_quote_item(CurrentRoute $currentRoute, QIR $qiR)
                                          : Response {
        $this->rbac();
        try {            
            $this->quote_item_service->deleteQuoteItem($this->quote_item($currentRoute,$qiR));
        } catch (Exception $e) {
            unset($e);
            $this->flash('danger', 'Cannot delete.');
        }
        $quote_id = $this->session->get('quote_id');
        return $this->factory->createResponse($this->viewRenderer->renderPartialAsString('/invoice/setting/quote_successful',
        ['heading'=>'','message'=>$this->sR->trans('record_successfully_deleted'),'url'=>'quote/view','id'=>$quote_id]));  
    }
    
    // Quote View Line 310: <form method="POST" class="form-inline" action="<?= $urlGenerator->generate('quote/delete_quote_tax_rate',['id'=>$tax_rate->id])>">
    public function delete_quote_tax_rate(CurrentRoute $currentRoute, QTRR $quotetaxrateRepository)
                                          : Response {
        $this->rbac();
        try {            
            $this->quote_tax_rate_service->deleteQuoteTaxRate($this->quotetaxrate($currentRoute,$quotetaxrateRepository));
        } catch (Exception $e) {
            unset($e);
            $this->flash('danger', 'Cannot delete.');
        }
        $quote_id = $this->session->get('quote_id');
        return $this->factory->createResponse($this->view_renderer->renderPartialAsString('/invoice/setting/quote_successful',
        ['heading'=>'','message'=>$this->sR->trans('record_successfully_deleted'),'url'=>'quote/view','id'=>$quote_id]));  
    }
    
    public function edit(ViewRenderer $head, Request $request, CurrentRoute $currentRoute,
                        ValidatorInterface $validator,
                        QR $quoteRepo,                        
                        IR $invRepo,
                        CR $clientRepo,
                        GR $groupRepo,
                        UR $userRepo,
                        SR $sR,
                        QAR $qaR,
                        CFR $cfR,
                        CVR $cvR,
                        QCR $qcR
    ): Response {
        $this->rbac();
        $quote_id = $this->quote($currentRoute, $quoteRepo,true)->getId(); 
        $action = ['quote/edit', ['id' => $quote_id]];
        $parameters = [
            'title' => '',
            'action' => $action,
            'errors' => [],
            'body' => $this->body($this->quote($currentRoute, $quoteRepo, true)),
            'head'=>$head,
            's'=>$this->sR,
            'invs'=>$invRepo->findAllPreloaded(),
            'clients'=>$clientRepo->findAllPreloaded(),
            'groups'=>$groupRepo->findAllPreloaded(),
            'users'=>$userRepo->findAll(),
            'numberhelper' => new NumberHelper($sR),
            'quote_statuses'=> $this->sR->getStatuses(),
            'cvH'=> new CVH($this->sR, new DateHelper($this->sR)),
            'custom_fields'=>$cfR->repoTablequery('quote_custom'),
            // Applicable to normally building up permanent selection lists eg. dropdowns
            'custom_values'=>$cvR->attach_hard_coded_custom_field_values_to_custom_field($cfR->repoTablequery('quote_custom')),
            // There will initially be no custom_values attached to this quote until they are filled in the field on the form
            'quote_custom_values' => $this->quote_custom_values($quote_id, $qcR),
        ];
        if ($request->getMethod() === Method::POST) {   
            $edited_body = $request->getParsedBody();
            $returned_form = $this->edit_save_form_fields($edited_body, $currentRoute, $validator, $quoteRepo, $groupRepo, $qaR);
            $parameters['body'] = $edited_body;
            $parameters['errors']=$returned_form->getFormErrors();
            $this->edit_save_custom_fields($edited_body, $validator, $qcR, $quote_id);            
            return $this->factory->createResponse($this->view_renderer->renderPartialAsString('/invoice/setting/quote_successful',
            ['heading'=>'','message'=>$this->sR->trans('record_successfully_updated'),'url'=>'quote/view','id'=>$quote_id]));  
        }
        return $this->view_renderer->render('_form', $parameters);
    }
    
    public function edit_save_form_fields($edited_body, $currentRoute, $validator, $quoteRepo, $groupRepo, $qaR) : QuoteForm {
        $form = new QuoteForm();
        if ($form->load($edited_body) && $validator->validate($form)->isValid()) {
                $this->quote_service->saveQuote($this->user_service->getUser(),$this->quote($currentRoute, $quoteRepo, true),$form,$this->sR, $groupRepo, $qaR);
        }
        return $form;
    }
    
    public function edit_save_custom_fields($parse, $validator, $qcR, $quote_id) {
        $custom = $parse['custom'];
        foreach ($custom as $custom_field_id => $value) {
            $quote_custom = $qcR->repoFormValuequery((string)$quote_id, (string)$custom_field_id);
            $quote_custom_input = [
                'quote_id'=>(int)$quote_id,
                'custom_field_id'=>(int)$custom_field_id,
                'value'=>(string)$value
            ];
            $form = new QuoteCustomForm();
            if ($form->load($quote_custom_input) && $validator->validate($form)->isValid())
            {
                $this->quote_custom_service->saveQuoteCustom($quote_custom, $form);     
            }
        }
    }
    
     //$this->flash
    private function flash($level, $message){
        $flash = new Flash($this->session);
        $flash->set($level, $message); 
        return $flash;
    }
    
    public function index(QAR $qaR, QR $quoteRepo, CR $clientRepo, GR $groupRepo, CurrentRoute $currentRoute, sR $sR): Response
    {
        $pageNum = (int)$currentRoute->getArgument('page', '1');
        //status 0 => 'all';
        $status = (int)$currentRoute->getArgument('status', '0');
        $paginator = (new OffsetPaginator($this->quotes($quoteRepo, $status)))
        ->withPageSize((int)$sR->setting('default_list_limit'))
        ->withCurrentPage($pageNum);       
        $canEdit = $this->rbac();       
        $parameters = [              
                'paginator' => $paginator,
                's'=> $this->sR,
                'alert'=>$this->view_renderer->renderPartialAsString('/invoice/layout/alert',[
                     'flash'=>$this->flash('', ''),
                ]),
                'canEdit' => $canEdit,
                'client_count'=>$clientRepo->count(),
                'quotes' => $this->quotes($quoteRepo, $status),
                'quote_statuses'=> $this->sR->getStatuses(),
                'status'=> $status,
                'max'=>(int)$sR->setting('default_list_limit'),
                'qaR'=>$qaR,
                'modal_create_quote'=>$this->view_renderer->renderPartialAsString('modal_create_quote',[
                      'clients'=>$clientRepo->findAllPreloaded(),
                      's'=>$this->sR,
                      'invoice_groups'=>$groupRepo->findAllPreloaded(),
                      'datehelper'=> new DateHelper($this->sR)
                 ])
        ];  
        return $this->view_renderer->render('index', $parameters);  
    }
    
    private function isAjaxRequest(Request $request): bool
    {
        return $request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
    }
    
    public function  items(string $items, ValidatorInterface $validator, $quote_id, int $order ,
                                     PR $pR, QIR $qir, QIAR $qiar, TRR $trr, UNR $unR) 
                                     : void {       
        foreach (Json::decode($items) as $item) {
            if ($item['item_name'] && (empty($item['item_id'])||!isset($item['item_id']))) {
                $ajax_content = new QuoteItemForm();
                $quoteitem = [];
                $quoteitem['name'] = $item['item_name'];
                $quoteitem['quote_id']=$item['quote_id'];
                $quoteitem['tax_rate_id']=$item['item_tax_rate_id'];
                $quoteitem['product_id']=($item['item_product_id']);
                //product_id used later to get description and name of product.
                $quoteitem['date_added']=new DateTimeImmutable();
                $quoteitem['quantity']=($item['item_quantity'] ? $this->number_helper->standardize_amount($item['item_quantity']) : floatval(0));
                $quoteitem['price']=($item['item_price'] ? $this->number_helper->standardize_amount($item['item_price']) : floatval(0));
                $quoteitem['discount_amount']= ($item['item_discount_amount']) ? $this->number_helper->standardize_amount($item['item_discount_amount']) : floatval(0);
                $quoteitem['order']= $order;
                $quoteitem['product_unit']=$unR->singular_or_plural_name($item['item_product_unit_id'],$item['item_quantity']);
                $quoteitem['product_unit_id']= ($item['item_product_unit_id'] ? $item['item_product_unit_id'] : null);                
                unset($item['item_id']);
                ($ajax_content->load($quoteitem) && $validator->validate($ajax_content)->isValid()) ? 
                $this->quote_item_service->saveQuoteItem(new QuoteItem(), $ajax_content, $quote_id, $pR, $trr, new QIAS($qiar),$qiar) : false;                 
                $order++;      
            }
            // Evaluate current items
            if ($item['item_name'] && (!empty($item['item_id'])||isset($item['item_id']))) {
                $unedited = $qir->repoQuoteItemquery($item['item_id']);  
                $ajax_content = new QuoteItemForm();
                $quoteitem = [];
                $quoteitem['name'] = $item['item_name'];
                $quoteitem['quote_id']=$item['quote_id'];
                $quoteitem['tax_rate_id']=$item['item_tax_rate_id'] ? $item['item_tax_rate_id'] : null;
                $quoteitem['product_id']=($item['item_product_id'] ? $item['item_product_id'] : null);
                //product_id used later to get description and name of product.
                $quoteitem['date_added']=new DateTimeImmutable();
                $quoteitem['quantity']=($item['item_quantity'] ? $this->number_helper->standardize_amount($item['item_quantity']) : floatval(0));
                $quoteitem['price']=($item['item_price'] ? $this->number_helper->standardize_amount($item['item_price']) : floatval(0));
                $quoteitem['discount_amount']= ($item['item_discount_amount']) ? $this->number_helper->standardize_amount($item['item_discount_amount']) : floatval(0);
                $quoteitem['order']= $order;
                $quoteitem['product_unit']=$unR->singular_or_plural_name($item['item_product_unit_id'],$item['item_quantity']);
                $quoteitem['product_unit_id']= ($item['item_product_unit_id'] ? $item['item_product_unit_id'] : null);                
                unset($item['item_id']);
                ($ajax_content->load($quoteitem) && $validator->validate($ajax_content)->isValid()) ? 
                $this->quote_item_service->saveQuoteItem($unedited, $ajax_content, $quote_id, $pR, $trr, new QIAS($qiar),$qiar) : false;             
            }      
        }
    }
    
    // Demo: Use form within $modalhelper using Helper/ModalHelper:  
    public function modalcreate(ViewRenderer $head, Request $request, 
                        ValidatorInterface $validator,
                        CR $clientRepo,
                        GR $groupRepo,
                        UR $userRepo,
                        QAR $qaR,
                        QR $quoteRepo,
                        SR $settingRepo,
    )
    {        
        $this->rbac();        
        $parameters = [
            'title' => 'Create',
            'action' => ['quote/modalcreate'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$this->sR,
            'head'=>$head,
            'quote'=>$quoteRepo->findAllPreloaded(),
            'clients'=>$clientRepo->findAllPreloaded(),
            'groups'=>$groupRepo->findAllPreloaded(),
            'users'=>$userRepo->findAll(),
            'datehelper'=> new DateHelper($settingRepo)
        ];
        
        if ($request->getMethod() === Method::POST) {
            
            $form = new QuoteForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->quote_service->saveQuote($this->user_service->getUser(),new Quote(),$form,$this->sR, $groupRepo, $qaR);
                return $this->web_service->getRedirectResponse('quote/index');
            }
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->view_renderer->renderPartial('modal_create_quote_form', $parameters);
    }
    
    // jquery function currently not used
    // Data parsed from quote.js:$(document).on('click', '#client_change_confirm', function () {
    public function modal_change_client(Request $request, CR $cR, SR $sR): Response 
    {
        if ($this->isAjaxRequest($request)) {
            $this->rbac();  
            $body = $request->getQueryParams() ?? [];
            $client = $cR->repoClientquery((string)$body['client_id']);
            $parameters = [
                'success'=>1,
                // Set a client id on quote/view.php so that details can be saved later. 
                'pre_save_client_id'=>$body['client_id'],                
                'client_address_1'=>$client->client_address_1.'<br>',
                'client_address_2'=>$client->client_address_2.'<br>',
                'client_townline'=>$client->client_city.'<br>'.$client->client_state.'<br>'.$client->client_zip.'<br>',
                'client_country'=>$client->client_country,
                'client_phone'=> $sR->trans('phone').'&nbsp;'.$client->client_phone,
                'client_mobile'=>$sR->trans('mobile').'&nbsp;'.$client->client_mobile,
                'client_fax'=>$sR->trans('fax').'&nbsp;'.$client->client_fax,
                'client_email'=>$sR->trans('email').'&nbsp;'. Html::link($client->client_email),                
                // Reset the a href id="after_client_change_url" link to the new client url
                'after_client_change_url'=>'/invoice/client/view/'.$body['client_id'],
                'after_client_change_name'=>$client->client_name,
            ];
            // return parameters to quote.js:client_change_confirm ajax success function for processing
            return $this->factory->createResponse(Json::encode($parameters));  
        }
    }
    
    // Called from quote.js quote_to_pdf_confirm_with_custom_fields
    public function pdf(CurrentRoute $currentRoute, CR $cR, CVR $cvR, CFR $cfR, QAR $qaR, QCR $qcR, QIR $qiR, QIAR $qiaR, QR $qR, QTRR $qtrR, SR $sR, UIR $uiR, Request $request) {
        // include is a value of 0 or 1 passed from quote.js function quote_to_pdf_with(out)_custom_fields indicating whether the user
        // wants custom fields included on the quote or not.
        $include = $currentRoute->getArgument('include');        
        $quote_id = $this->session->get('quote_id');
        $quote_amount = (($qaR->repoQuoteAmountCount($quote_id) > 0) ? $qaR->repoQuotequery($quote_id) : null);
        $custom = (($include===(string)1) ? true : false);
        $quote_custom_values = $this->quote_custom_values($this->session->get('quote_id'),$qcR);
        // session is passed to the pdfHelper and will be used for the locale ie. $session->get('_language') or the print_language ie $session->get('print_language')
        $pdfhelper = new PdfHelper($sR, $this->session);
        // The quote will be streamed ie. shown, and not archived
        $stream = true;
        // If we are required to mark quotes as 'sent' when sent.
        if ($sR->setting('mark_quotes_sent_pdf') == 1) {
            $this->generate_quote_number_if_applicable($quote_id);
            $sR->mark_sent($quote_id);
        }
        $quote = $qR->repoQuoteUnloadedquery((string)$quote_id);        
        $pdfhelper->generate_quote_pdf($quote_id, $quote->getUser_id(), $stream, $custom, $quote_amount, $quote_custom_values, $cR, $cvR, $cfR, $qiR, $qiaR, $qR, $qtrR, $uiR, $this->view_renderer);        
    }
    
    public function generate_quote_number_if_applicable($quote_id, QR $qR, SR $sR) : void
    {
        $quote = $qR->repoQuoteUnloadedquery($quote_id);
        if (!empty($quote) && ($quote->getStatus_id() == 1) && ($quote->getNumber() == "")) {
                // Generate new quote number if applicable
                if ($sR->get_setting('generate_quote_number_for_draft') == 0) {
                    $quote_number = $qR->get_quote_number($quote->getGroup_id());
                    // Set new quote number and save
                    $quote->setNumber($quote_number);
                    $qR->save();
                }            
        }
    }
    
    private function quote(CurrentRoute $currentRoute,QuoteRepository $quoteRepo, $unloaded = false) 
    {
        $id = $currentRoute->getArgument('id');
        $quote = ($unloaded ? $quoteRepo->repoQuoteUnLoadedquery($id) : $quoteRepo->repoQuoteLoadedquery($id));
        if ($quote === null) {
            return $this->web_service->getNotFoundResponse();
        }
        return $quote;
    }
    
    private function quotes(QuoteRepository $quoteRepo, $status) 
    {
        $quotes = $quoteRepo->findAllWithStatus($status);    
        if ($quotes === null) {
            return $this->web_service->getNotFoundResponse();
        }
        return $quotes;
    }
    
    public function quote_custom_values($quote_id, qcR $qcR) : array
    {
        // Get all the custom fields that have been registered with this quote on creation, retrieve existing values via repo, and populate 
        // custom_field_form_values array
        $custom_field_form_values = [];
        if ($qcR->repoQuoteCount($quote_id) > 0) {
            $quote_custom_fields = $qcR->repoFields($quote_id);
            foreach ($quote_custom_fields as $key => $val) {
                $custom_field_form_values['custom[' . $key . ']'] = $val;
            }
        }
        return $custom_field_form_values;
    }
    
    private function quote_item(CurrentRoute $currentRoute,QIR $quoteitemRepository) 
    {
        $id = $currentRoute->getArgument('id');       
        $quoteitem = $quoteitemRepository->repoQuoteItemquery($id);
        if ($quoteitem === null) {
            return $this->web_service->getNotFoundResponse();
        }
        return $quoteitem;
    }
    
    // Data fed from quote.js->$(document).on('click', '#quote_to_invoice_confirm', function () {
    public function quote_to_invoice_confirm(Request $request, ValidatorInterface $validator, 
                                             GR $gR, IIAR $iiaR, InvItemAmountservice $iiaS, PR $pR, QAR $qaR, QCR $qcR,
                                             QIAR $qiaR, QIR $qiR,QR $qR, QTRR $qtrR, TRR $trR, UNR $unR) : Response
    {
        $this->rbac($this->session);  
        $body = $request->getQueryParams() ?? [];
        $quote_id = (string)$body['quote_id'];
        $quote = $qR->repoQuoteUnloadedquery($quote_id);
        $ajax_body = [
            'client_id'=>$body['client_id'],
            'group_id'=>$body['group_id'],
            // current user_id filled in below because rbac access to the quote
            'status_id'=>1,
            'is_read_only'=>0,
            'password'=>$body['password'],
            'number'=>$gR->generate_invoice_number((string)$body['group_id']),
            'discount_amount'=>floatval($quote->getDiscount_amount()),
            'discount_percent'=>floatval($quote->getDiscount_percent()),
            'url_key'=>$quote->getUrl_key(),
            'payment_method'=>0,
            'terms'=>'',
            'creditinvoice_parent_id'=>''
        ];
        $form = new InvForm();
        $inv = new Inv();
        if (($form->load($ajax_body) && $validator->validate($form)->isValid()) &&
                // Quote has not been copied before:  inv_id = 0
                (($quote->getInv_id()===(string)0))
            ) {    
            $this->inv_service->saveInv($this->user_service->getUser(),$inv, $form, $this->sR, $gR);
            $inv_id = $inv->getId();
            // Transfer each quote_item to inv_item and the corresponding quote_item_amount to inv_item_amount for each item
            $this->quote_to_invoice_quote_items($quote_id,$inv_id, $iiaR, $iiaS, $pR,$qiR, $trR, $validator, $unR);
            $this->quote_to_invoice_quote_tax_rates($quote_id,$inv_id,$qtrR, $validator);
            $this->quote_to_invoice_quote_custom($quote_id,$inv_id,$qcR, $validator);
            $this->quote_to_invoice_quote_amount($quote_id,$inv_id,$qaR, $validator);
            // Update the quotes inv_id.
            $quote->setInv_id($inv_id);
            $qR->save($quote);
            $parameters = ['success'=>1];
            //return response to quote.js to reload page at location
            return $this->factory->createResponse(Json::encode($parameters));          
        } else {
            $parameters = [
               'success'=>0,
            ];
            //return response to quote.js to reload page at location
            return $this->factory->createResponse(Json::encode($parameters));          
        } 
    }
    
    private function quote_to_invoice_quote_items($quote_id, $inv_id, $iiaR, $iiaS, $pR, $qiR, $trR, $validator, $unR) {
        // Get all items that belong to the quote
        $items = $qiR->repoQuoteItemIdquery((string)$quote_id);
        foreach ($items as $quote_item) {
            $inv_item = [
                'inv_id'=>$inv_id,
                'tax_rate_id'=>$quote_item->getTax_rate_id(),
                'product_id'=>$quote_item->getProduct_id(),
                'task_id'=>'',
                'name'=>$quote_item->getName(),
                'description'=>$quote_item->getDescription(),
                'quantity'=>$quote_item->getQuantity(),
                'price'=>$quote_item->getPrice(),
                'discount_amount'=>$quote_item->getDiscount_amount(),
                'order'=>$quote_item->getOrder(),
                'is_recurring'=>0,
                'product_unit'=>$quote_item->getProduct_unit(),
                'product_unit_id'=>$quote_item->getProduct_unit_id(),
                // Recurring date
                'date'=>''
            ];
            // Create an equivalent invoice item for the quote item
            $invitem = new InvItem();
            $form = new InvItemForm();
            if ($form->load($inv_item) && $validator->validate($form)->isValid()) {
                $this->inv_item_service->saveInvItem($invitem, $form, $inv_id, $pR, $trR , $iiaS, $iiaR, $unR);
            }
        }
    }
    
    private function quote_to_invoice_quote_tax_rates($quote_id, $inv_id, $qtrR, $validator) {
        // Get all tax rates that have been setup for the quote
        $quote_tax_rates = $qtrR->repoQuotequery($quote_id);        
        foreach ($quote_tax_rates as $quote_tax_rate){            
            $inv_tax_rate = [
                'inv_id'=>$inv_id,
                'tax_rate_id'=>$quote_tax_rate->getTax_rate_id(),
                'include_item_tax'=>$quote_tax_rate->getInclude_item_tax(),
                'amount'=>$quote_tax_rate->getQuote_tax_rate_amount(),
            ];
            $entity = new InvTaxRate();
            $form = new InvTaxRateForm();
            if ($form->load($inv_tax_rate) && $validator->validate($form)->isValid()) {    
                $this->inv_tax_rate_service->saveInvTaxRate($entity,$form);
            }
        }        
    }
    
    private function quote_to_invoice_quote_custom($quote_id, $inv_id, $qcR, $validator) {
        $quote_customs = $qcR->repoFields($quote_id);
        foreach ($quote_customs as $quote_custom) {
            $inv_custom = [
                'inv_id'=>$inv_id,
                'custom_field_id'=>$quote_custom->getCustom_field_id(),
                'value'=>$quote_custom->getValue(),
            ];
            $entity = new InvCustom();
            $form = new InvCustomForm();
            if ($form->load($inv_custom) && $validator->validate($form)->isValid()) {    
                $this->inv_custom_service->saveInvCustom($entity,$form);            
            }
        }        
    }
    
    private function quote_to_invoice_quote_amount($quote_id,$inv_id,$qaR, $validator) : void {
        $quote_amount = $qaR->repoQuotequery((string)$quote_id);
        $inv_amount = [
            'inv_id'=>$inv_id,
            'sign'=>1,
            'item_subtotal'=>$quote_amount->getItem_subtotal(),
            'item_tax_total'=>$quote_amount->getItem_tax_total(),
            'tax_total'=>$quote_amount->getTax_total(),
            'total'=>$quote_amount->getTotal(),
            'paid'=>floatval(0.00),
            'balance'=>floatval(0.00),
        ];
        $entity = new InvAmount();
        $form = new InvAmountForm();
        if ($form->load($inv_amount) && $validator->validate($form)->isValid()) {    
                $this->inv_amount_service->saveInvAmount($entity,$form);            
        }
    }
    
    private function quote_to_quote_quote_amount($quote_id,$copy_id) {
        $this->quote_amount_service->initializeCopyQuoteAmount(new QuoteAmount(), $quote_id, $copy_id);                
    }
    
     // Data fed from quote.js->$(document).on('click', '#quote_to_quote_confirm', function () {
    public function quote_to_quote_confirm(Request $request, ValidatorInterface $validator, 
                                           GR $gR, QIAS $qiaS, PR $pR, QAR $qaR, QCR $qcR,
                                           QIAR $qiaR, QIR $qiR, QR $qR, QTRR $qtrR, TRR $trR, UNR $unR) : Response
    {
        $this->rbac();  
        $data_quote_js = $request->getQueryParams() ?? [];
        $quote_id = (string)$data_quote_js['quote_id'];
        $original = $qR->repoQuoteUnloadedquery($quote_id);
        $group_id = $original->getGroup_id();
        $ajax_body = [
                'inv_id'=>null,
                'client_id'=>$data_quote_js['client_id'],
                'group_id'=>$group_id,
                'status_id'=>1,
                'number'=>$gR->generate_invoice_number((string)$group_id),  
                'discount_amount'=>floatval($original->getDiscount_amount()),
                'discount_percent'=>floatval($original->getDiscount_percent()),
                'url_key'=>'',
                'password'=>'',              
                'notes'=>'',
        ];
        $form = new QuoteForm();
        $copy = new Quote();
        if (($form->load($ajax_body) && $validator->validate($form)->isValid())) {    
            $this->quote_service->saveQuote($this->user_service->getUser(), $copy, $form, $this->sR, $gR, $qaR);            
            // Transfer each quote_item to quote_item and the corresponding quote_item_amount to quote_item_amount for each item
            $copy_id =$copy->getId();
            $this->quote_to_quote_quote_items($quote_id,$copy_id, $qiaR, $qiaS, $pR,$qiR, $trR, $unR, $validator);
            $this->quote_to_quote_quote_tax_rates($quote_id,$copy_id,$qtrR, $validator);
            $this->quote_to_quote_quote_custom($quote_id,$copy_id,$qcR, $validator);
            $this->quote_to_quote_quote_amount($quote_id,$copy_id);            
            $qR->save($copy);
            $parameters = ['success'=>1];
            //return response to quote.js to reload page at location
            return $this->factory->createResponse(Json::encode($parameters));          
        } else {
            $parameters = [
               'success'=>0,
            ];
            //return response to quote.js to reload page at location
            return $this->factory->createResponse(Json::encode($parameters));          
        } 
    }
    
    private function quote_to_quote_quote_custom($quote_id, $copy_id, $qcR, $validator) {
        $quote_customs = $qcR->repoFields($quote_id);
        foreach ($quote_customs as $quote_custom) {
            $copy_custom = [
                'quote_id'=>$copy_id,
                'custom_field_id'=>$quote_custom->getCustom_field_id(),
                'value'=>$quote_custom->getValue(),
            ];
            $entity = new QuoteCustom();
            $form = new QuoteCustomForm();
            if ($form->load($copy_custom) && $validator->validate($form)->isValid()) {    
                $this->quote_custom_service->saveQuoteCustom($entity,$form);            
            }
        }        
    }
    
    private function quote_to_quote_quote_items($quote_id, $copy_id, $qiaR, $qiaS, $pR, $qiR, $trR, $unR, $validator) {
        // Get all items that belong to the original quote
        $items = $qiR->repoQuoteItemIdquery((string)$quote_id);
        foreach ($items as $quote_item) {
            $copy_item = [
                'quote_id'=>$copy_id,
                'tax_rate_id'=>$quote_item->getTax_rate_id(),
                'product_id'=>$quote_item->getProduct_id(),
                'task_id'=>'',
                'name'=>$quote_item->getName(),
                'description'=>$quote_item->getDescription(),
                'quantity'=>$quote_item->getQuantity(),
                'price'=>$quote_item->getPrice(),
                'discount_amount'=>$quote_item->getDiscount_amount(),
                'order'=>$quote_item->getOrder(),
                'is_recurring'=>0,
                'product_unit'=>$quote_item->getProduct_unit(),
                'product_unit_id'=>$quote_item->getProduct_unit_id(),
                // Recurring date
                'date'=>''
            ];
            // Create an equivalent invoice item for the quote item
            $copyitem = new QuoteItem();
            $form = new QuoteItemForm();
            if ($form->load($copy_item) && $validator->validate($form)->isValid()) {
                $this->quote_item_service->saveQuoteItem($copyitem, $form, $copy_id, $pR, $trR , $qiaS, $qiaR, $unR);
            }
        }
    }
    
    private function quote_to_quote_quote_tax_rates($quote_id, $copy_id, $qtrR, $validator) {
        // Get all tax rates that have been setup for the quote
        $quote_tax_rates = $qtrR->repoQuotequery($quote_id);        
        foreach ($quote_tax_rates as $quote_tax_rate){            
            $copy_tax_rate = [
                'quote_id'=>$copy_id,
                'tax_rate_id'=>$quote_tax_rate->getTax_rate_id(),
                'include_item_tax'=>$quote_tax_rate->getInclude_item_tax(),
                'amount'=>$quote_tax_rate->getQuote_tax_rate_amount(),
            ];
            $entity = new QuoteTaxRate();
            $form = new QuoteTaxRateForm();
            if ($form->load($copy_tax_rate) && $validator->validate($form)->isValid()) {    
                $this->quote_tax_rate_service->saveQuoteTaxRate($entity,$form);
            }
        }        
    }
    
    private function quotetaxrate(CurrentRoute $currentRoute, QTRR $quotetaxrateRepository) 
    {
        $id = $currentRoute->getArgument('id');       
        $quotetaxrate = $quotetaxrateRepository->repoQuoteTaxRatequery($id);
        if ($quotetaxrate === null) {
            return $this->web_service->getNotFoundResponse();
        }
        return $quotetaxrate;
    }
    
    private function rbac() 
    {
        $canEdit = $this->user_service->hasPermission('editQuote');
        if (!$canEdit){
            $this->flash('warning', $this->translator->translate('invoice.permission'));
            return $this->web_service->getRedirectResponse('quote/index');
        }
        return $canEdit;
    }
    
    //Currently not used.
    //receive data ie. body and items from structure parsed from
    //src/invoice/asset/rebuild-1.13/js/quote.js->$('#btn_save_quote').click(function () into queryparams    
    
    public function save(ValidatorInterface $validator, Request $request, 
                    PR $pR, TRR $trR, QAR $qaR, QCR $qcR, QIAR $qiaR, QIR $qiR, QR $qR, QTRR $qtrR, GR $gR, UNR $unR, CR $cR)
                    : Response
    {
        $parameters = [];
        $parameters['success'] = 0; 
        // if ($this->isAjaxRequest($request)) {
        $this->rbac();       
        //$body_and_items = $request->getQueryParams() ?? [];
        $body_and_items = $request->getParsedBody() ?? [];
        //divide
        $items = $body_and_items['items'] ? $body_and_items['items'] : [];
        //$custom = !empty($body_and_items['custom']) || isset($body_and_items['custom'])  ? $body_and_items['custom'] : '';
        $custom = $body_and_items['custom'] ? $body_and_items['custom'] : [];
        //$quote_id = $body_and_items['quote_id'] ? $body_and_items['quote_id'] : '';
        $quote_id = $this->session->get('quote_id');
        // If the client has been changed retrieve pre_save_client_id from quote/view.php
        $pre_save_client_id = $body_and_items['pre_save_client_id'];
        if (!empty($pre_save_client_id)) {$client = $cR->repoClientquery($pre_save_client_id);}
        $quote = $qR->repoQuoteUnLoadedquery($quote_id);
        //compile ajax quote body separate from quote items
        $data = [
            'id'=>$body_and_items['quote_id'],                
            'date_created'=>$body_and_items['quote_date_created'],
            'status_id'=> $body_and_items['quote_status_id'],
             // If no Quote Number, generate a quote number if quote does not have Draft status.
            'number'=>($body_and_items['quote_number'] === '' && $body_and_items['quote_status_id'] != 1) ? $gR->generate_invoice_number($quote->getGroup_id()) : $body_and_items['quote_number'],
            'discount_amount'=>($body_and_items['quote_discount_amount'] === '') ? floatval(0) : $body_and_items['quote_discount_amount'],
            'discount_percent'=>($body_and_items['quote_discount_percent'] === '') ? floatval(0) : $body_and_items['quote_discount_percent'],
            'password'=>$body_and_items['quote_password'],
            'notes'=>$body_and_items['notes'],        
            'inv_id'=>$quote->getInv_id(),
            'user_id'=>$quote->getUser_id(), 
            'client_id'=>!empty($pre_save_client_id) ? (int)$client->getClient_id() : (int)$quote->getClient_id(),
            'group_id'=>$quote->getGroup_id(),
            'date_modified'=>$quote->getDate_modified(),                
            'url_key'=>$quote->getUrl_key(),
        ];
        //serialized array of custom fields
        $custom_field_body = [            
            'custom'=>$custom,            
        ];        
        $ajax_content = new QuoteForm();
        // No validation is required since the values have been calculated in the NumberHelper            
        if ($ajax_content->load($data) && $validator->validate($ajax_content)->isValid()) {    
            $this->quote_service->saveQuote($this->user_service->getUser(),$quote,$ajax_content,$this->sR, $gR, $qaR);
            // Give a sequential sort order number to the quote item so that it appears sequentially on the quote
            // Count the current items in the quote. If there are no items ie. first item sort_order = 0 + 1 = 1
            $sort_order = $qiR->repoCount($quote_id)+1;            
            $this->items($items, $validator, $quote_id, $sort_order, $pR, $qiR, $qiaR, $trR, $unR);
            $this->custom_fields($validator, $custom_field_body,[],$quote_id, $qcR);
            $this->number_helper->calculate_quote($quote_id, $qiR, $qiaR, $qtrR, $qaR, $qR); 
            $parameters =[
                'success'=>1,
                'flash'=>$this->flash('success', $this->sR->trans('record_successfully_updated')),
                'quote_number'=>($body_and_items['quote_number'] === '' && $body_and_items['quote_status_id'] != 1) ? $gR->generate_invoice_number($quote->getGroup_id()) : $body_and_items['quote_number'],
                'quote_date_created'=>$body_and_items['quote_date_created'],
                'status_id'=>$body_and_items['quote_status_id'],
                'password'=>$body_and_items['quote_password'],                
                'discount_amount'=>($body_and_items['quote_discount_amount'] === '') ? floatval(0) : $body_and_items['quote_discount_amount'],
                'discount_percent'=>($body_and_items['quote_discount_percent'] === '') ? floatval(0) : $body_and_items['quote_discount_percent'],
                //partial_item_table.php id="amount_subtotal"
                //partial_item_table.php id="amount_quote_total"
                'items'=>$items,
                'notes'=>$body_and_items['notes'],
                'custom'=>$body_and_items['custom'],
            ];
            return $this->view_renderer->render('view', $parameters);
        }        
       //} //this is ajaxRequest 
    }
    
    // quote/view => '#btn_save_quote_custom_fields' => quote_custom_field.js => /invoice/quote/save_custom";
    public function save_custom(ValidatorInterface $validator, Request $request, QCR $qcR) : Response
    {
            $parameters['success'] = 0;
            $this->rbac();       
            $js_data = $request->getQueryParams() ?? [];        
            $quote_id = $js_data['quote_id'];
            $custom_field_body = [            
                'custom'=>$js_data['custom'] ?: '',            
            ];
            $this->custom_fields($validator, $custom_field_body,$quote_id, $qcR);
            $parameters =[
                'success'=>1,
            ];
            return $this->factory->createResponse(Json::encode($parameters)); 
    }
    
    
    // Not being used
    public function save_quote_custom_values($cfR, $quote) : void 
    {
        // If there are any custom fields associated with quotes build these for this quote
        $custom_fields = $cfR->repoTablequery('quote_custom');
        // Check the custom fields for quote_custom
        // For each of these 'quote_custom' create a record in the quote custom table
        foreach ($custom_fields as $custom_field) {                    
            $ajax_quote_custom = new QuoteCustomForm();
            $quote_custom = [];
            $quote_custom['quote_id'] = $quote->getId();
            $quote_custom['custom_field_id'] = $custom_field->id;
            $quote_custom['value'] = '';
            $ajax_quote_custom->load($quote_custom);  
            $this->quote_custom_service->saveQuoteCustom(new QuoteCustom(), $ajax_quote_custom);
        }
    }
    
    // '#quote_tax_submit' => quote.js 
    public function save_quote_tax_rate(Request $request, ValidatorInterface $validator)
                                        : Response {
      if ($this->isAjaxRequest($request)) {
            $this->rbac();  
            $body = $request->getQueryParams() ?? [];
            $ajax_body = [
                'quote_id'=>$body['quote_id'],
                'tax_rate_id'=>$body['tax_rate_id'],
                'include_item_tax'=>$body['include_item_tax'],
                'quote_tax_rate_amount'=>floatval(0.00),
            ];
            $ajax_content = new QuoteTaxRateForm();
            if ($ajax_content->load($ajax_body) && $validator->validate($ajax_content)->isValid()) {    
                $this->quote_tax_rate_service->saveQuoteTaxRate(new QuoteTaxRate(), $ajax_content);
                $parameters = [
                    'success'=>1
                ];
                //return response to quote.js to reload page at location
                return $this->factory->createResponse(Json::encode($parameters));          
            } else {
                $parameters = [
                   'success'=>0
                 ];
                //return response to quote.js to reload page at location
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
                         CFR $cfR, CVR $cvR, IR $iR, PR $pR, QAR $qaR, QIAR  $qiaR, QIR $qiR, QR $qR, QTRR $qtrR, TRR $trR, FR $fR,  UNR $uR, CR $cR, GR $gR, QCR $qcR)
                         : Response {
        $this->rbac();
        $this->session->set('quote_id',$this->quote($currentRoute, $qR, false)->getId());
        $this->number_helper->calculate_quote($this->session->get('quote_id'), $qiR, $qiaR, $qtrR, $qaR, $qR); 
        $quote_tax_rates = (($qtrR->repoCount($this->session->get('quote_id')) > 0) ? $qtrR->repoQuotequery($this->session->get('quote_id')) : null); 
        $quote_amount = (($qaR->repoQuoteAmountCount($this->session->get('quote_id')) > 0) ? $qaR->repoQuotequery($this->session->get('quote_id')) : null);
        $quote_custom_values = $this->quote_custom_values($this->session->get('quote_id'),$qcR);
        $parameters = [
            'title' => $this->sR->trans('view'),            
            'body' => $this->body($this->quote($currentRoute, $qR, false)),          
            's'=>$this->sR,
            'alert'=>$this->view_renderer->renderPartialAsString('/invoice/layout/alert',[
                    'flash'=>$this->flash('', ''),
                    'errors' => [],
            ]),
            'add_quote_item'=>$this->view_renderer->renderPartialAsString('/invoice/quoteitem/_item_form',[
                    'action' => ['quoteitem/add'],
                    'errors' => [],
                    'body' => $request->getParsedBody(),
                    's'=>$this->sR,
                    'head'=>$head,
                    'quote_id'=>$this->quote($currentRoute, $qR, true),
                    'tax_rates'=>$trR->findAllPreloaded(),
                    'products'=>$pR->findAllPreloaded(),
                    'units'=>$uR->findAllPreloaded(),
                    'numberhelper'=>new NumberHelper($this->sR)
            ]),
            // Get all the fields that have been setup for this SPECIFIC quote in quote_custom. 
            'fields' => $qcR->repoFields($this->session->get('quote_id')),
            // Get the standard extra custom fields built for EVERY quote. 
            'custom_fields'=>$cfR->repoTablequery('quote_custom'),
            'custom_values'=>$cvR->attach_hard_coded_custom_field_values_to_custom_field($cfR->repoTablequery('quote_custom')),
            'cvH'=> new CVH($this->sR),
            'quote_custom_values' => $quote_custom_values,
            'quote_statuses'=> $this->sR->getStatuses(),  
            'quote'=>$qR->repoQuoteLoadedquery($this->session->get('quote_id')),   
            'partial_item_table'=>$this->view_renderer->renderPartialAsString('/invoice/quote/partial_item_table',[
                //'modalhelper'=>new ModalHelper($this->sR),
                'numberhelper'=> new NumberHelper($this->sR),          
                'products'=>$pR->findAllPreloaded(),
                'quote_items'=>$qiR->repoQuotequery($this->session->get('quote_id')),
                'quote_item_amount'=>$qiaR,
                'quote_tax_rates'=>$quote_tax_rates,
                'quote_amount'=> $quote_amount,
                'quote'=>$qR->repoQuoteLoadedquery($this->session->get('quote_id')),  
                's'=>$this->sR,
                'tax_rates'=>$trR->findAllPreloaded(),
                'units'=>$uR->findAllPreloaded(),
            ]),
            'modal_choose_items'=>$this->view_renderer->renderPartialAsString('/invoice/product/modal_product_lookups_quote',
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
            'modal_add_quote_tax'=>$this->view_renderer->renderPartialAsString('modal_add_quote_tax',['s'=>$this->sR,'tax_rates'=>$trR->findAllPreloaded()]),
            //'modalhelper'=> new ModalHelper($this->sR),
            'modal_copy_quote'=>$this->view_renderer->renderPartialAsString('modal_copy_quote',[ 's'=>$this->sR,
                'quote'=>$qR->repoQuoteLoadedquery($this->session->get('quote_id')),
                'clients'=>$cR->findAllPreloaded(),                
                'groups'=>$gR->findAllPreloaded(),
            ]),
            'modal_delete_quote'=>$this->view_renderer->renderPartialAsString('modal_delete_quote',
                    ['action'=>['quote/delete', ['id' => $this->session->get('quote_id')]],
                     's'=>$this->sR,   
            ]),            
            'modal_delete_items'=>$this->view_renderer->renderPartialAsString('/invoice/quote/modal_delete_item',[
                    'partial_item_table_modal'=>$this->view_renderer->renderPartialAsString('/invoice/quoteitem/_partial_item_table_modal',[
                        'quoteitems'=>$qiR->repoQuotequery($this->session->get('quote_id')),
                        's'=>$this->sR,
                        'numberhelper'=>new NumberHelper($this->sR),
                    ]),
                    's'=>$this->sR,
            ]),
            'modal_quote_to_invoice'=>$this->view_renderer->renderPartialAsString('modal_quote_to_invoice',[
                     's'=>$this->sR,
                     'quote'=> $this->quote($currentRoute, $qR, true),                        
                     'groups'=>$gR->findAllPreloaded(),
            ]),
            'modal_quote_to_pdf'=>$this->view_renderer->renderPartialAsString('modal_quote_to_pdf',[
                     's'=>$this->sR,
                     'quote'=> $this->quote($currentRoute, $qR, true),                        
            ]),
            'dropzone_quote_html'=>$this->view_renderer->renderPartialAsString('dropzone_quote_html',[
                     's'=>$this->sR,
            ]),
            'view_custom_fields'=>$this->view_renderer->renderPartialAsString('view_custom_fields', [
                     'custom_fields'=>$cfR->repoTablequery('quote_custom'),
                     'custom_values'=>$cvR->attach_hard_coded_custom_field_values_to_custom_field($cfR->repoTablequery('quote_custom')),
                     'quote_custom_values'=> $quote_custom_values,  
                     'cvH'=> new CVH($this->sR),
                     's'=>$this->sR,   
            ]),        
        ];
        return $this->view_renderer->render('view', $parameters);
    }
}