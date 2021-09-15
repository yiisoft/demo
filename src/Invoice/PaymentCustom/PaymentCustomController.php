<?php

declare(strict_types=1); 

namespace App\Invoice\PaymentCustom;

use App\Invoice\Entity\PaymentCustom;
use App\Invoice\PaymentCustom\PaymentCustomService;
use App\Invoice\PaymentCustom\PaymentCustomRepository;
use App\Invoice\Setting\SettingRepository;
use App\Invoice\Payment\PaymentRepository;
use App\User\UserService;
use Yiisoft\Validator\ValidatorInterface;
use App\Service\WebControllerService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Http\Method;
use Yiisoft\Yii\View\ViewRenderer;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Session\Flash\Flash;

final class PaymentCustomController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService;
    private PaymentCustomService $paymentcustomService;
    
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UserService $userService,
        PaymentCustomService $paymentcustomService
    )    
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/paymentcustom')
                                           ->withLayout(dirname(dirname(__DIR__)) .'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->userService = $userService;
        $this->paymentcustomService = $paymentcustomService;
    }
    
    public function index(SessionInterface $session, PaymentCustomRepository $paymentcustomRepository, SettingRepository $settingRepository, Request $request, PaymentCustomService $service): Response
    {
       
         $canEdit = $this->rbac($session);
         $flash = $this->flash($session, 'dummy' , 'Flash message!.');
         $parameters = [
      
          's'=>$settingRepository,
          'canEdit' => $canEdit,
          'paymentcustoms' => $this->paymentcustoms($paymentcustomRepository),
          'flash'=> $flash
         ];

        if ($this->isAjaxRequest($request)) {
            return $this->viewRenderer->renderPartial('_paymentcustoms', ['data' => $paginator]);
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
                        PaymentRepository $paymentRepository
    )
    {
        $this->rbac($session);
        $parameters = [
            'title' => 'Add',
            'action' => ['paymentcustom/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$settingRepository,
            'head'=>$head,
            
            'payments'=>$paymentRepository->findAllPreloaded(),
        ];
        
        if ($request->getMethod() === Method::POST) {
            
            $form = new PaymentCustomForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->paymentcustomService->savePaymentCustom(new PaymentCustom(),$form);
                return $this->webService->getRedirectResponse('paymentcustom/index');
            }
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function edit(ViewRenderer $head, SessionInterface $session, Request $request, 
                        ValidatorInterface $validator,
                        PaymentCustomRepository $paymentcustomRepository, 
                        SettingRepository $settingRepository,                        
                        PaymentRepository $paymentRepository
    ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => 'Edit',
            'action' => ['paymentcustom/edit', ['id' => $this->paymentcustom($request, $paymentcustomRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->paymentcustom($request, $paymentcustomRepository)),
            'head'=>$head,
            's'=>$settingRepository,
                        'payments'=>$paymentRepository->findAllPreloaded()
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new PaymentCustomForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->paymentcustomService->savePaymentCustom($this->paymentcustom($request,$paymentcustomRepository), $form);
                return $this->webService->getRedirectResponse('paymentcustom/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function delete(SessionInterface $session,Request $request,PaymentCustomRepository $paymentcustomRepository 
    ): Response {
        $this->rbac($session);
       
        $this->paymentcustomService->deletePaymentCustom($this->paymentcustom($request,$paymentcustomRepository));               
        return $this->webService->getRedirectResponse('paymentcustom/index');        
    }
    
    public function view(SessionInterface $session,Request $request,PaymentCustomRepository $paymentcustomRepository,
        SettingRepository $settingRepository,
        ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('view'),
            'action' => ['paymentcustom/edit', ['id' => $this->paymentcustom($request, $paymentcustomRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->paymentcustom($request, $paymentcustomRepository)),
            's'=>$settingRepository,             
            'paymentcustom'=>$paymentcustomRepository->repoPaymentCustomquery($this->paymentcustom($request, $paymentcustomRepository)->getId()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }
    
    private function rbac(SessionInterface $session) 
    {
        $canEdit = $this->userService->hasPermission('editPaymentCustom');
        if (!$canEdit){
            $this->flash($session,'warning', 'You do not have the required permission.');
            return $this->webService->getRedirectResponse('paymentcustom/index');
        }
        return $canEdit;
    }
    
    private function paymentcustom(Request $request,PaymentCustomRepository $paymentcustomRepository) 
    {
        $id = $request->getAttribute('id');       
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
        };
        return $paymentcustoms;
    }
    
    private function body($paymentcustom) {
        $body = [
                
          'id'=>$paymentcustom->getId(),
          'payment_id'=>$paymentcustom->getPayment_id(),
          'fieldid'=>$paymentcustom->getFieldid(),
          'fieldvalue'=>$paymentcustom->getFieldvalue()
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