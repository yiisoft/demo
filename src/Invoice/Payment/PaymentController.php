<?php

declare(strict_types=1); 

namespace App\Invoice\Payment;

use App\Invoice\Entity\Payment;
use App\Invoice\Payment\PaymentService;
use App\Invoice\Payment\PaymentRepository;
use App\Invoice\Setting\SettingRepository;
use App\Invoice\Inv\InvRepository;
use App\Invoice\PaymentMethod\PaymentMethodRepository;
use App\User\UserService;
use Yiisoft\Validator\ValidatorInterface;
use App\Service\WebControllerService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Http\Method;
use Yiisoft\Yii\View\ViewRenderer;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Session\Flash\Flash;

final class PaymentController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService;
    private PaymentService $paymentService;
    
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UserService $userService,
        PaymentService $paymentService
    )    
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/payment')
                                           ->withLayout(dirname(dirname(__DIR__)) .'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->userService = $userService;
        $this->paymentService = $paymentService;
    }
    
    public function index(SessionInterface $session, PaymentRepository $paymentRepository, SettingRepository $settingRepository, Request $request, PaymentService $service): Response
    {
       
         $canEdit = $this->rbac($session);
         $flash = $this->flash($session, 'dummy' , 'Flash message!.');
         $parameters = [
      
          's'=>$settingRepository,
          'canEdit' => $canEdit,
          'payments' => $this->payments($paymentRepository),
          'flash'=> $flash
         ];

        if ($this->isAjaxRequest($request)) {
            return $this->viewRenderer->renderPartial('_payments', ['data' => $paginator]);
        }
        
        return $this->viewRenderer->render('index', $parameters);
    }
    
    private function isAjaxRequest(Request $request): bool
    {
        return $request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
    }
    
    public function add(ViewRenderer $head,SessionInterface $session, Request $request, 
                        ValidatorInterface $validator,
                        SettingRepository $settingRepository,                        
                        InvRepository $invRepository,
                        PaymentMethodRepository $payment_methodRepository
    )
    {
        $this->rbac($session);
        $parameters = [
            'title' => 'Add',
            'action' => ['payment/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$settingRepository,
            'head'=>$head,
            
            'invs'=>$invRepository->findAllPreloaded(),
            'payment_methods'=>$payment_methodRepository->findAllPreloaded(),
        ];
        
        if ($request->getMethod() === Method::POST) {
            
            $form = new PaymentForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->paymentService->savePayment(new Payment(),$form);
                return $this->webService->getRedirectResponse('payment/index');
            }
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function edit(ViewRenderer $head, SessionInterface $session, Request $request, 
                        ValidatorInterface $validator,
                        PaymentRepository $paymentRepository, 
                        SettingRepository $settingRepository,                        
                        InvRepository $invRepository,
                        PaymentMethodRepository $payment_methodRepository
    ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => 'Edit',
            'action' => ['payment/edit', ['id' => $this->payment($request, $paymentRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->payment($request, $paymentRepository)),
            'head'=>$head,
            's'=>$settingRepository,
            'invs'=>$invRepository->findAllPreloaded(),
            'payment_methods'=>$payment_methodRepository->findAllPreloaded()
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new PaymentForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->paymentService->savePayment($this->payment($request,$paymentRepository), $form);
                return $this->webService->getRedirectResponse('payment/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function delete(SessionInterface $session,Request $request,PaymentRepository $paymentRepository 
    ): Response {
        $this->rbac($session);
        try {
              $this->paymentService->deletePayment($this->payment($request,$paymentRepository));               
              return $this->webService->getRedirectResponse('payment/index');
	} catch (Exception $e) {
              unset($e);
              $this->flash($session, 'danger', 'Cannot delete. Payment history exists.');
              return $this->webService->getRedirectResponse('payment/index');
        }
    }
    
    public function view(SessionInterface $session,Request $request,PaymentRepository $paymentRepository,
        SettingRepository $settingRepository
        ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('view'),
            'action' => ['payment/edit', ['id' => $this->payment($request, $paymentRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->payment($request, $paymentRepository)),
            's'=>$settingRepository,             
            'payment'=>$paymentRepository->repoPaymentquery($this->payment($request, $paymentRepository)->getId()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }
    
    private function rbac(SessionInterface $session) 
    {
        $canEdit = $this->userService->hasPermission('editPayment');
        if (!$canEdit){
            $this->flash($session,'warning', 'You do not have the required permission.');
            return $this->webService->getRedirectResponse('payment/index');
        }
        return $canEdit;
    }
    
    private function payment(Request $request,PaymentRepository $paymentRepository) 
    {
        $id = $request->getAttribute('id');       
        $payment = $paymentRepository->repoPaymentquery($id);
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
        };
        return $payments;
    }
    
    private function body($payment) {
        $body = [      
          'id'=>$payment->getId(),
          'payment_method_id'=>$payment->getPayment_method_id(),
          'date'=>$payment->getDate(),
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
}

?>