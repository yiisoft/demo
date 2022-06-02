<?php

declare(strict_types=1); 

namespace App\Invoice\PaymentMethod;

use App\Invoice\Entity\PaymentMethod;
use App\Invoice\PaymentMethod\PaymentMethodService;
use App\Invoice\PaymentMethod\PaymentMethodRepository;
use App\Invoice\Setting\SettingRepository;

use App\User\UserService;
use App\Service\WebControllerService;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Yiisoft\Http\Method;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Session\Flash\Flash;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\View\ViewRenderer;

final class PaymentMethodController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService;
    private PaymentMethodService $paymentmethodService;
    private TranslatorInterface $translator;
    
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UserService $userService,
        PaymentMethodService $paymentmethodService,
        TranslatorInterface $translator
    )    
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/paymentmethod')
                                           ->withLayout(dirname(dirname(__DIR__)) .'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->userService = $userService;
        $this->paymentmethodService = $paymentmethodService;
        $this->translator = $translator;
    }
    
    public function index(SessionInterface $session, PaymentMethodRepository $paymentmethodRepository, SettingRepository $settingRepository, Request $request, PaymentMethodService $service): Response
    {
       
         $canEdit = $this->rbac($session);
         $flash = $this->flash($session, '','');
         $parameters = [
      
          's'=>$settingRepository,
          'canEdit' => $canEdit,
          'payment_methods' => $this->paymentmethods($paymentmethodRepository),
          'flash'=> $flash
         ];

        if ($this->isAjaxRequest($request)) {
            return $this->viewRenderer->renderPartial('_paymentmethods', ['data' => $paginator]);
        }
        
        return $this->viewRenderer->render('index', $parameters);
    }
    
    private function isAjaxRequest(Request $request): bool
    {
        return $request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
    }
    
    public function add(ViewRenderer $head,SessionInterface $session, Request $request, 
                        ValidatorInterface $validator,
                        SettingRepository $settingRepository                        

    )
    {
        $this->rbac($session);
        $parameters = [
            'title' => 'Add',
            'action' => ['paymentmethod/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$settingRepository,
            'head'=>$head,
            
        ];
        
        if ($request->getMethod() === Method::POST) {
            
            $form = new PaymentMethodForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->paymentmethodService->savePaymentMethod(new PaymentMethod(),$form);
                return $this->webService->getRedirectResponse('paymentmethod/index');
            }
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function edit(ViewRenderer $head, SessionInterface $session, Request $request, CurrentRoute $currentRoute,
                        ValidatorInterface $validator,
                        PaymentMethodRepository $paymentmethodRepository, 
                        SettingRepository $settingRepository                        

    ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => 'Edit',
            'action' => ['paymentmethod/edit', ['id' => $this->paymentmethod($currentRoute, $paymentmethodRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->paymentmethod($currentRoute, $paymentmethodRepository)),
            'head'=>$head,
            's'=>$settingRepository,            
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new PaymentMethodForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->paymentmethodService->savePaymentMethod($this->paymentmethod($currentRoute, $paymentmethodRepository), $form);
                return $this->webService->getRedirectResponse('paymentmethod/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function delete(SessionInterface $session,CurrentRoute $currentRoute, PaymentMethodRepository $paymentmethodRepository 
    ): Response {
        $this->rbac($session);
        try {
            $this->paymentmethodService->deletePaymentMethod($this->paymentmethod($currentRoute, $paymentmethodRepository));               
            return $this->webService->getRedirectResponse('paymentmethod/index'); 
	} catch (Exception $e) {
            unset($e);
            $this->flash($session, 'danger', 'Cannot delete. Payment Method history exists.');
            return $this->webService->getRedirectResponse('paymentmethod/index'); 
        }
    }
    
    public function view(SessionInterface $session, CurrentRoute $currentRoute, PaymentMethodRepository $paymentmethodRepository,
        SettingRepository $settingRepository
        ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('view'),
            'action' => ['paymentmethod/edit', ['id' => $this->paymentmethod($currentRoute, $paymentmethodRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->paymentmethod($currentRoute, $paymentmethodRepository)),
            's'=>$settingRepository,             
            'paymentmethod'=>$paymentmethodRepository->repoPaymentMethodquery($this->paymentmethod($currentRoute, $paymentmethodRepository)->getId()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }
    
    private function rbac(SessionInterface $session) 
    {
        $canEdit = $this->userService->hasPermission('editPaymentMethod');
        if (!$canEdit){
            $this->flash($session,'warning', $this->translator->translate('invoice.permission'));
            return $this->webService->getRedirectResponse('paymentmethod/index');
        }
        return $canEdit;
    }
    
    private function paymentmethod(CurrentRoute $currentRoute, PaymentMethodRepository $paymentmethodRepository) 
    {
        $id = $currentRoute->getArgument('id');       
        $paymentmethod = $paymentmethodRepository->repoPaymentMethodquery($id);
        if ($paymentmethod === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $paymentmethod;
    }
    
    private function paymentmethods(PaymentMethodRepository $paymentmethodRepository) 
    {
        $paymentmethods = $paymentmethodRepository->findAllPreloaded();        
        if ($paymentmethods === null) {
            return $this->webService->getNotFoundResponse();
        };
        return $paymentmethods;
    }
    
    private function body($paymentmethod) {
        $body = [
                
          'id'=>$paymentmethod->getId(),
          'name'=>$paymentmethod->getName()
                ];
        return $body;
    }
    
    private function flash(SessionInterface $session, $level, $message){
        $flash = new Flash($session);
        $flash->set($level, $message); 
        return $flash;
    }
}