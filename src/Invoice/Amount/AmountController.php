<?php

declare(strict_types=1); 

namespace App\Invoice\Amount;

use App\Invoice\Entity\Amount;
use App\Invoice\Amount\AmountService;
use App\Invoice\Amount\AmountRepository;
use App\Invoice\Setting\SettingRepository;
use App\Invoice\Inv\InvRepository;
use App\User\UserService;
use Yiisoft\Validator\ValidatorInterface;
use App\Service\WebControllerService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Http\Method;
use Yiisoft\Yii\View\ViewRenderer;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Session\Flash\Flash;

final class AmountController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService;
    private AmountService $amountService;
    
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UserService $userService,
        AmountService $amountService
    )    
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/amount')
                                           ->withLayout(dirname(dirname(__DIR__)).'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->userService = $userService;
        $this->amountService = $amountService;
    }
    
    public function index(SessionInterface $session, AmountRepository $amountRepository, SettingRepository $settingRepository, Request $request, AmountService $service): Response
    {
       
         $canEdit = $this->rbac($session);
         $flash = $this->flash($session, 'success' , 'Change the type from success to info and you will get a flash message!.');
         $parameters = [
          's'=>$settingRepository,
          'canEdit' => $canEdit,
          'amounts' => $this->amounts($amountRepository),
          'flash'=> $flash
         ];

        if ($this->isAjaxRequest($request)) {
            return $this->viewRenderer->renderPartial('_amounts', ['data' => $paginator]);
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
                        InvRepository $invRepository
    )
    {
        $this->rbac($session);
        $parameters = [
            'title' => 'Add',
            'action' => ['amount/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$settingRepository,
            'head'=>$head,            
            'invs'=>$invRepository->findAllPreloaded(),
        ];
        
        if ($request->getMethod() === Method::POST) {
            
            $form = new AmountForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->amountService->saveAmount(new Amount(),$form);
                return $this->webService->getRedirectResponse('amount/index');
            }
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function edit(ViewRenderer $head, SessionInterface $session, Request $request, 
                        ValidatorInterface $validator,
                        AmountRepository $amountRepository, 
                        SettingRepository $settingRepository,                        
                        InvRepository $invRepository
    ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => 'Edit',
            'action' => ['amount/edit', ['id' => $this->amount($request, $amountRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->amount($request, $amountRepository)),
            's'=>$settingRepository,
            'head'=>$head,
            'invs'=>$invRepository->findAllPreloaded()
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new AmountForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->amountService->saveAmount($this->amount($request,$amountRepository), $form);
                return $this->webService->getRedirectResponse('amount/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function delete(SessionInterface $session,Request $request,AmountRepository $amountRepository 
    ): Response {
        $this->rbac($session);
        $this->flash($session, 'danger','This record has been deleted');
        $this->amountService->deleteAmount($this->amount($request,$amountRepository));               
        return $this->webService->getRedirectResponse('amount/index');        
    }
    
    public function view(SessionInterface $session,Request $request,AmountRepository $amountRepository,
        SettingRepository $settingRepository,
        ValidatorInterface $validator
        ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('view'),
            'action' => ['amount/edit', ['id' => $this->amount($request, $amountRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->amount($request, $amountRepository)),
            's'=>$settingRepository,
            //load Entity\Product BelongTo relations ie. $family, $tax_rate, $unit by means of repoProductQuery             
            'amount'=>$amountRepository->repoAmountquery($this->amount($request, $amountRepository)->getId()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }
    
    private function rbac(SessionInterface $session) 
    {
        $canEdit = $this->userService->hasPermission('editAmount');
        if (!$canEdit){
            $this->flash($session,'warning', 'You do not have the required permission.');
            return $this->webService->getRedirectResponse('amount/index');
        }
        return $canEdit;
    }
    
    private function amount(Request $request,AmountRepository $amountRepository) 
    {
        $id = $request->getAttribute('id');       
        $amount = $amountRepository->repoAmountquery($id);
        if ($amount === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $amount;
    }
    
    private function amounts(AmountRepository $amountRepository) 
    {
        $amounts = $amountRepository->findAllPreloaded();        
        if ($amounts === null) {
            return $this->webService->getNotFoundResponse();
        };
        return $amounts;
    }
    
    private function body($amount) {
        $body = [      
          'id'=>$amount->getId(),
          'inv_id'=>$amount->getInv_id(),
          'sign'=>$amount->getSign(),
          'item_sub_total'=>$amount->getItem_sub_total(),
          'item_tax_total'=>$amount->getItem_tax_total(),
          'tax_total'=>$amount->getTax_total(),
          'invoice_total'=>$amount->getInvoice_total(),
          'invoice_paid'=>$amount->getInvoice_paid(),
          'invoice_balance'=>$amount->getInvoice_balance()
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