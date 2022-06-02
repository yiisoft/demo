<?php

declare(strict_types=1); 

namespace App\Invoice\Payment;

use App\Invoice\Client\ClientRepository;
use App\Invoice\Entity\Payment;
use App\Invoice\Entity\PaymentCustom;
use App\Invoice\Helpers\CustomValuesHelper;
use App\Invoice\Helpers\DateHelper;
use App\Invoice\Helpers\NumberHelper;
use App\Invoice\Helpers\ClientHelper;
use App\Invoice\Inv\InvRepository;
use App\Invoice\InvAmount\InvAmountRepository;
use App\Invoice\Payment\PaymentService;
use App\Invoice\Payment\PaymentRepository;
use App\Invoice\Payment\PaymentForm;
use App\Invoice\PaymentMethod\PaymentMethodRepository;
use App\Invoice\PaymentCustom\PaymentCustomRepository;
use App\Invoice\PaymentCustom\PaymentCustomForm;
use App\Invoice\PaymentCustom\PaymentCustomService;
use App\Invoice\Setting\SettingRepository;
use App\Invoice\CustomField\CustomFieldRepository;
use App\Invoice\CustomValue\CustomValueRepository;
use App\Invoice\InvItemAmount\InvItemAmountRepository as IIAR;
use App\Invoice\InvItem\InvItemRepository as IIR;
use App\Invoice\InvTaxRate\InvTaxRateRepository as ITRR;

use App\User\UserService;
use App\Service\WebControllerService;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Http\Method;
use Yiisoft\Json\Json;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Session\Flash\Flash;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\View\ViewRenderer;
use \Exception;
use \DateTime;

final class PaymentController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService;
    private PaymentService $paymentService;
    private PaymentCustomService $paymentCustomService;
    private TranslatorInterface $translator;
    
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UserService $userService,
        PaymentService $paymentService,
        PaymentCustomService $paymentCustomService,    
        TranslatorInterface $translator
    )    
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/payment')
                                           ->withLayout(dirname(dirname(__DIR__)) .'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->userService = $userService;
        $this->paymentService = $paymentService;
        $this->paymentCustomService = $paymentCustomService;
        $this->translator = $translator;
    }
        
    public function index(SessionInterface $session, PaymentRepository $paymentRepository, SettingRepository $settingRepository, DateHelper $dateHelper, Request $request, PaymentService $service): Response
    {   
        $pageNum = (int)$request->getAttribute('page', '1');
        $paginator = (new OffsetPaginator($this->payments($paymentRepository)))
         ->withPageSize((int)$settingRepository->setting('default_list_limit'))
         ->withCurrentPage($pageNum);
        $canEdit = $this->rbac($session);
        $parameters = [
            'alert'=>$this->viewRenderer->renderPartialAsString('/invoice/layout/alert',[
                'flash'=>$this->flash($session,'', ''),
                'errors' => [],
            ]),
            'paginator' => $paginator,
            's'=>$settingRepository,
            'd'=>$dateHelper,
            'canEdit'=>$canEdit,
            'payments'=>$this->payments($paymentRepository),
        ];
        return $this->viewRenderer->render('index', $parameters);  
    }
    
    public function add(ViewRenderer $head, SessionInterface $session, Request $request, 
                        ValidatorInterface $validator,
                        SettingRepository $settingRepository,                        
                        InvRepository $invRepository,
                        InvAmountRepository $iaR,
                        PaymentMethodRepository $payment_methodRepository,
                        PaymentCustomRepository $pcR,
                        PaymentRepository $pmtR,
                        CustomFieldRepository $cfR,
                        CustomValueRepository $cvR,
                        ClientRepository $cR,
                        IIR $iiR,
                        IIAR $iiaR,
                        ITRR $itrR,                        
    )
    {
        $this->rbac($session);
        $open = $invRepository->open();
        $amounts = [];
        $invoice_payment_methods = [];
        foreach ($open as $open_invoice) {
            $inv_amount = $iaR->repoInvquery($open_invoice->getId());            
            $amounts['invoice' . $open_invoice->getId()] = $settingRepository->format_amount($inv_amount->getBalance());
            $invoice_payment_methods['invoice' . $open_invoice->getId()] = $open_invoice->getPayment_method();
        }
        $number_helper = new NumberHelper($settingRepository);
        $parameters = [
            'action' => ['payment/add'],          
            'body' => $request->getParsedBody(),
            'alert'=>$this->viewRenderer->renderPartialAsString('/invoice/layout/alert',[
                    'flash'=>$this->flash($session,'', ''),
                    'errors' => [],
            ]),
            's'=>$settingRepository,
            'datehelper'=> new DateHelper($settingRepository),
            'numberhelper'=> $number_helper,
            'clienthelper'=> new ClientHelper(),
            'head'=>$head,            
            'open_invs'=>$open,
            // jquery script at bottom of _from to load all amounts
            'amounts'=>Json::encode($amounts),
            'invoice_payment_methods'=>Json::encode($invoice_payment_methods),
            'payment_methods'=>$payment_methodRepository->findAllPreloaded(),
            'cR'=>$cR,
            'iaR'=>$iaR,
            'cvH'=> new CustomValuesHelper($settingRepository, new DateHelper($settingRepository)),
            'custom_fields'=>$cfR->repoTablequery('payment_custom'),
            // Applicable to normally building up permanent selection lists eg. dropdowns
            'custom_values'=>$cvR->attach_hard_coded_custom_field_values_to_custom_field($cfR->repoTablequery('payment_custom')),
            // There will initially be no custom_values attached to this payment until they are filled in the field on the form
            //'payment_custom_values' => $this->payment_custom_values($payment_id,$pcR),
            'payment_custom_values' => [],
            'edit'=>false
        ];
        try { 
                if ($request->getMethod() === Method::POST) {
                    $body = $parameters['body'];                    
                    // Retrieve form values
                    foreach ($body as $key => $value) {
                        switch ($key) {
                            case 'inv_id':
                                $inv_id = (int)$value;
                                break;
                            case 'payment_date':
                                $payment_date = new DateTime($value);
                                break;
                            case 'amount':
                                $amount = (float)$value;
                                break;
                            case 'payment_method_id':
                                $payment_method_id = (int)$value;
                                break;                            
                            case 'note':
                                $note = (string)$value;
                                break;                            
                        }
                    }                    
                    $payment = new Payment();
                    $payment->setPayment_method_id($payment_method_id);
                    $payment->setPayment_date($payment_date);
                    $payment->setAmount($amount);
                    $payment->setNote($note);
                    $payment->setInv_id($inv_id); 
                    $pmtR->save($payment);
                    
                    // Once the payment has been saved, retrieve the payment id for the custom fields
                    $payment_id = $payment->getId();
                    
                    // Recalculate the invoice
                    $number_helper->calculate_inv($inv_id, $iiR, $iiaR, $itrR, $iaR, $invRepository, $pmtR);
                    $this->flash($session, 'info', $settingRepository->trans('record_successfully_created')); 
                                        
                    // Retrieve the custom array
                    $custom = $body['custom'];
                    foreach ($custom as $custom_field_id => $value) {
                        $payment_custom = new PaymentCustom();
                        $payment_custom_input = [
                            'payment_id'=>$payment_id,
                            'custom_field_id'=>$custom_field_id,
                            'value'=>$value
                        ];
                        $form = new PaymentCustomForm();
                        if ($form->load($payment_custom_input) 
                            && $validator->validate($form)->isValid() 
                            && $this->add_custom_field($payment_id, $custom_field_id, $pcR)) {
                            try {
                              $this->paymentCustomService->savePaymentCustom($payment_custom, $form);
                            } catch (Exception $e){
                                switch ($e->getCode()) {
                                    //catch integrity constraint on custom_field_id => 23000
                                    case 23000 :
                                       //$message = 'Incomplete fields.'. ' Payment: '.$payment->getId(). ' Custom field id: '.$custom_field_id.' Value: '.$value . var_dump($payment);
                                       $message = $payment_id; 
                                       break;
                                    default : 
                                       $message = 'Unknown error.';
                                       break;
                                }   
                                $this->flash($session, 'danger', $message . ' ' . $e->getCode());
                                unset($e);   
                            }
                        }
                    }
                    
                    return $this->webService->getRedirectResponse('payment/index');
                    //$parameters['errors'] = $form->getFormErrors();
                }
                return $this->viewRenderer->render('_form', $parameters);
        } catch (Exception $e) {
                unset($e);
                $this->flash($session, 'info', 'Fill in all the fields.');
                return $this->viewRenderer->render('_form', $parameters);
        }
        return $this->viewRenderer->render('_form', $parameters); 
    }
    
    
    
    public function edit(ViewRenderer $head, SessionInterface $session, Request $request, CurrentRoute $currentRoute,
                        ValidatorInterface $validator,
                        SettingRepository $settingRepository,                          
                        InvRepository $invRepository,
                        InvAmountRepository $iaR,                        
                        PaymentRepository $pmtR, 
                        PaymentMethodRepository $payment_methodRepository,
                        PaymentCustomRepository $pcR,            
                        CustomFieldRepository $cfR,
                        CustomValueRepository $cvR,
                        ClientRepository $cR,
                        IIR $iiR,
                        IIAR $iiaR,
                        ITRR $itrR,
    ): Response {
        $this->rbac($session);        
        $payment = $this->payment($currentRoute, $pmtR);
        $payment_id = $payment->getId();
        $open = $invRepository->open();
        $amounts = [];
        $invoice_payment_methods = [];
        $number_helper = new NumberHelper($settingRepository);
        $date_helper = new DateHelper($settingRepository);
        $parameters = [
            'title' => 'Edit',
            'action' => ['payment/edit', ['id' => $payment_id]],        
            'body' => $this->body($this->payment($currentRoute, $pmtR)),
            'alert'=>$this->viewRenderer->renderPartialAsString('/invoice/layout/alert',[
                    'flash'=>$this->flash($session,'', ''),
                    'errors' => [],
            ]),
            'errors'=>[],
            's'=>$settingRepository,
            'datehelper'=> $date_helper,
            'numberhelper'=> $number_helper,
            'clienthelper'=>new ClientHelper(),
            'head'=>$head, 
            'open_invs'=>$open,
            // jquery script at bottom of _from to load all amounts
            'amounts'=>Json::encode($amounts),
            'invoice_payment_methods'=>Json::encode($invoice_payment_methods),
            'payment_methods'=>$payment_methodRepository->findAllPreloaded(),
            'cR'=>$cR,
            'iaR'=>$iaR,
            'cvH'=> new CustomValuesHelper($settingRepository, new DateHelper($settingRepository)),
            'custom_fields'=>$cfR->repoTablequery('payment_custom'),
            // Applicable to normally building up permanent selection lists eg. dropdowns
            'custom_values'=>$cvR->attach_hard_coded_custom_field_values_to_custom_field($cfR->repoTablequery('payment_custom')),
            // There will initially be no custom_values attached to this payment until they are filled in the field on the form
            //'payment_custom_values' => $this->payment_custom_values($payment_id,$pcR),
            'payment_custom_values' => $this->payment_custom_values($payment_id, $pcR),
            'edit'=>true
       ];
       if ($request->getMethod() === Method::POST) {
            $edited_body = $request->getParsedBody();
            $inv_id = $edited_body['inv_id'];           
            $returned_form = $this->edit_save_form_fields($edited_body, $currentRoute, $validator, $pmtR);
            $parameters['body'] = $edited_body;
            $parameters['errors']=$returned_form->getFormErrors();
            $this->edit_save_custom_fields($edited_body, $validator, $pcR, $payment_id);
            // Recalculate the invoice
            $number_helper->calculate_inv($inv_id, $iiR, $iiaR, $itrR, $iaR, $invRepository, $pmtR);
            $this->flash($session, 'info', $settingRepository->trans('record_successfully_created')); 
            return $this->webService->getRedirectResponse('payment/index');
       }
       return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function edit_save_form_fields($edited_body, $currentRoute, $validator, $pmtR) : PaymentForm {
        $form = new PaymentForm();
        if ($form->load($edited_body) && $validator->validate($form)->isValid()) {
                $this->paymentService->savePayment($this->payment($currentRoute, $pmtR), $form);
        }
        return $form;
    }
    
    public function edit_save_custom_fields($parse, $validator, $pcR,$payment_id) {
        $custom = $parse['custom'];
        foreach ($custom as $custom_field_id => $value) {
            $payment_custom = $pcR->repoFormValuequery((string)$payment_id, (string)$custom_field_id);
            $payment_custom_input = [
                'payment_id'=>(int)$payment_id,
                'custom_field_id'=>(int)$custom_field_id,
                'value'=>(string)$value
            ];
            $form = new PaymentCustomForm();
            if ($form->load($payment_custom_input) && $validator->validate($form)->isValid())
            {
                $this->paymentCustomService->savePaymentCustom($payment_custom, $form);     
            }
        }
    }
    
    // If the custom field already exists return false
    public function add_custom_field($payment_id, $custom_field_id, $pcR): bool
    {
        return ($pcR->repoPaymentCustomCount((string)$payment_id, (string)$custom_field_id) > 0 ? false : true);        
    }
    
    public function custom_fields(ValidatorInterface $validator, $array, $payment_id, $pcR) : void
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
                $from_custom = new PaymentCustomForm();
                $payment_custom = [];
                $payment_custom['payment_id']=$payment_id;
                $payment_custom['custom_field_id']=$key;
                $payment_custom['value']=$value; 
                $model = ($pcR->repoPaymentCustomCount($payment_id,(string)$key) > 0 ? $pcR->repoFormValuequery($payment_id,(string) $key) : new PaymentCustom());
                ($from_custom->load($payment_custom) && $validator->validate($from_custom)->isValid()) ? 
                     $this->paymentCustomService->savePaymentCustom($model, $from_custom) : '';                                   
               }
            }             
        } 
    }
    
    public function delete(SessionInterface $session, CurrentRoute $currentRoute, PaymentRepository $paymentRepository 
    ): Response {
        $this->rbac($session);
        try {
              // Error: Unprocessible Entity : If <form Method="POST" in payment/index line 70 used and
              // and 'if ($request->getMethod() === Method::POST) {' used here in association with this delete function.
              // config/route payment/delete has both GET and POST METHOD.
              $this->paymentService->deletePayment($this->payment($currentRoute, $paymentRepository));
              $this->flash($session, 'danger', 'Deleted.');
              return $this->webService->getRedirectResponse('payment/index');
	} catch (Exception $e) {
              unset($e);
              $this->flash($session, 'danger', 'Cannot delete.');
              return $this->webService->getRedirectResponse('payment/index');
        }
    }
    
    public function view(SessionInterface $session, CurrentRoute $currentRoute, PaymentRepository $paymentRepository,
        SettingRepository $settingRepository
        ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('view'),
            'action' => ['payment/edit', ['id' => $this->payment($currentRoute, $paymentRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->payment($currentRoute, $paymentRepository)),
            's'=>$settingRepository,             
            'payment'=>$paymentRepository->repoPaymentquery($this->payment($currentRoute, $paymentRepository)->getId()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }
    
    private function rbac(SessionInterface $session) 
    {
        $canEdit = $this->userService->hasPermission('editPayment');
        if (!$canEdit){
            $this->flash($session,'warning', $this->translator->translate('invoice.permission'));
            return $this->webService->getRedirectResponse('payment/index');
        }
        return $canEdit;
    }
    
    private function payment(CurrentRoute $currentRoute, PaymentRepository $paymentRepository) 
    {
        $id = $currentRoute->getArgument('id');       
        $payment = $paymentRepository->repoPaymentquery((string)$id);
        if ($payment === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $payment;
    }
    
    private function payments(PaymentRepository $paymentRepository) 
    {
        $payments = $paymentRepository->findAllPreloaded();        
        if ($payments === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $payments;
    }
    
    private function body($payment) {
        $body = [      
          'id'=>$payment->getId(),
          'payment_method_id'=>$payment->getPayment_method_id(),
          'payment_date'=>$payment->getPayment_date(),
          'amount'=>$payment->getAmount(),
          'note'=>$payment->getNote(),
          'inv_id'=>$payment->getInv_id()
        ];
        return $body;
    }
    
    private function flash(SessionInterface $session, $level, $message){
        $flash = new Flash($session);
        $flash->set($level, $message); 
        return $flash;
    }
    
    private function payment_custom_values($payment_id, PaymentCustomRepository $pcR) : array
    {
        // Function edit: Get field's values for editing
        $custom_field_form_values = [];
        if ($pcR->repoPaymentCount($payment_id) > 0) {
          $payment_custom_fields = $pcR->repoFields((string)$payment_id);
          foreach ($payment_custom_fields as $key => $val) {
               $custom_field_form_values['custom[' . $key . ']'] = $val;
          }
        }
        return $custom_field_form_values;
    }
    
    // payment/view => '#btn_save_payment_custom_fields' => payment_custom_field.js => /invoice/payment/save_custom";
    public function save_custom(ValidatorInterface $validator, Request $request, PaymentCustomRepository $pcR) : Response
    {
            $parameters['success'] = 0;
            $this->rbac();       
            $js_data = $request->getQueryParams() ?? [];        
            $payment_id = $js_data['payment_id'];
            $custom_field_body = [            
                'custom'=>$js_data['custom'] ?: '',            
            ];
            $this->custom_fields($validator, $custom_field_body, $payment_id, $pcR);
            $parameters =[
                'success'=>1,
            ];
            return $this->factory->createResponse(Json::encode($parameters)); 
    }
}