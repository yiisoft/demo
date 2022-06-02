<?php

declare(strict_types=1); 

namespace App\Invoice\PaymentCustom;

use App\Invoice\CustomField\CustomFieldRepository;
use App\Invoice\Entity\PaymentCustom;
use App\Invoice\Helpers\DateHelper;
use App\Invoice\PaymentCustom\PaymentCustomService;
use App\Invoice\PaymentCustom\PaymentCustomRepository;
use App\Invoice\Payment\PaymentRepository;
use App\Invoice\Setting\SettingRepository;
use App\User\UserService;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Yiisoft\Http\Method;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Yii\View\ViewRenderer;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Session\Flash\Flash;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\ValidatorInterface;
use App\Service\WebControllerService;
use \Exception;

final class PaymentCustomController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService;
    private PaymentCustomService $paymentcustomService;
    private TranslatorInterface $translator;
        
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UserService $userService,
        PaymentCustomService $paymentcustomService,
        TranslatorInterface $translator
    )    
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/paymentcustom')
                                           ->withLayout(dirname(dirname(__DIR__)).'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->userService = $userService;
        $this->paymentcustomService = $paymentcustomService;
        $this->translator = $translator;
    }
    
    public function index(SessionInterface $session, PaymentCustomRepository $paymentcustomRepository, SettingRepository $settingRepository, Request $request, PaymentCustomService $service): Response
    {      
         $canEdit = $this->rbac($session);
         $flash = $this->flash($session, '','');
         $parameters = [
      
      
          's'=>$settingRepository,
          'canEdit' => $canEdit,
          'paymentcustoms' => $this->paymentcustoms($paymentcustomRepository),
          'flash'=> $flash
         ];
        
        return $this->viewRenderer->render('index', $parameters);
    }
    
    private function isAjaxRequest(Request $request): bool
    {
        return $request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
    }
    
    public function add(ViewRenderer $head,SessionInterface $session, Request $request, 
                        ValidatorInterface $validator,
                        SettingRepository $settingRepository,                        
                        PaymentRepository $paymentRepository,
                        CustomFieldRepository $custom_fieldRepository
    )
    {
        $this->rbac($session);
        $parameters = [
            'title' => 'Add',
            'action' => ['paymentcustom/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$settingRepository,
            'datehelper'=>new DateHelper($settingRepository),
            'head'=>$head,            
            'payments'=>$paymentRepository->findAllPreloaded(),
            'custom_fields'=>$custom_fieldRepository->findAllPreloaded(),
        ];
        
        if ($request->getMethod() === Method::POST) {
            
            $form = new PaymentCustomForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->paymentcustomService->savePaymentCustom(new PaymentCustom(),$form);
                return $this->webService->getRedirectResponse('paymentcustom/index');
            }
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function edit(ViewRenderer $head, SessionInterface $session, Request $request, CurrentRoute $currentRoute,
                        ValidatorInterface $validator,
                        PaymentCustomRepository $paymentcustomRepository, 
                        SettingRepository $settingRepository,                        
                        PaymentRepository $paymentRepository,
                        CustomFieldRepository $custom_fieldRepository
    ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => 'Edit',
            'action' => ['paymentcustom/edit', ['id' => $this->paymentcustom($currentRoute, $paymentcustomRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->paymentcustom($currentRoute, $paymentcustomRepository)),
            'head'=>$head,
            's'=>$settingRepository,
                        'payments'=>$paymentRepository->findAllPreloaded(),
            'custom_fields'=>$custom_fieldRepository->findAllPreloaded()
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new PaymentCustomForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->paymentcustomService->savePaymentCustom($this->paymentcustom($currentRoute,$paymentcustomRepository), $form);
                return $this->webService->getRedirectResponse('paymentcustom/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function delete(SessionInterface $session, CurrentRoute $currentRoute, PaymentCustomRepository $paymentcustomRepository 
    ): Response {
        $this->rbac($session);
        try {
            $this->paymentcustomService->deletePaymentCustom($this->paymentcustom($currentRoute, $paymentcustomRepository));               
            $this->flash($session, 'info', 'Deleted.');
            return $this->webService->getRedirectResponse('paymentcustom/index'); 
	} catch (Exception $e) {
            //unset($e);
            $this->flash($session, 'danger', $e);
            return $this->webService->getRedirectResponse('paymentcustom/index'); 
        }
    }
    
    public function view(SessionInterface $session, CurrentRoute $currentRoute, PaymentCustomRepository $paymentcustomRepository,
        SettingRepository $settingRepository,
        ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('view'),
            'action' => ['paymentcustom/view', ['id' => $this->paymentcustom($currentRoute, $paymentcustomRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->paymentcustom($currentRoute, $paymentcustomRepository)),
            's'=>$settingRepository,             
            'paymentcustom'=>$paymentcustomRepository->repoPaymentCustomquery($this->paymentcustom($currentRoute, $paymentcustomRepository)->getId()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }
        
    private function rbac(SessionInterface $session) 
    {
        $canEdit = $this->userService->hasPermission('editPaymentCustom');
        if (!$canEdit){
            $this->flash($session,'warning', $this->translator->translate('invoice.permission'));
            return $this->webService->getRedirectResponse('paymentcustom/index');
        }
        return $canEdit;
    }
    
    private function paymentcustom(CurrentRoute $currentRoute, PaymentCustomRepository $paymentcustomRepository) 
    {
        $id = $currentRoute->getArgument('id');       
        $paymentcustom = $paymentcustomRepository->repoPaymentCustomquery($id);
        if ($paymentcustom === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $paymentcustom;
    }
    
    private function paymentcustoms(PaymentCustomRepository $paymentcustomRepository) 
    {
        $paymentcustoms = $paymentcustomRepository->findAllPreloaded();        
        if ($paymentcustoms === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $paymentcustoms;
    }
    
    private function body($paymentcustom) {
        $body = [                
          'id'=>$paymentcustom->getId(),
          'payment_id'=>$paymentcustom->getPayment_id(),
          'custom_field_id'=>$paymentcustom->getCustom_field_id(),
          'value'=>$paymentcustom->getValue()
        ];
        return $body;
    }
    
    private function flash(SessionInterface $session, $level, $message){
        $flash = new Flash($session);
        $flash->set($level, $message); 
        return $flash;
    }
}

