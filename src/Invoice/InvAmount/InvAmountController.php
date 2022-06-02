<?php

declare(strict_types=1); 

namespace App\Invoice\InvAmount;

use App\Invoice\Entity\InvAmount;
use App\Invoice\InvAmount\InvAmountService;
use App\Invoice\InvAmount\InvAmountRepository;
use App\Invoice\Setting\SettingRepository;
use App\Invoice\Inv\InvRepository;

use App\User\UserService;
use App\Service\WebControllerService;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Yiisoft\Http\Method;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Session\Flash\Flash;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\View\ViewRenderer;

final class InvAmountController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService;
    private TranslatorInterface $translator;
    
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UserService $userService,
        InvAmountService $invamountService,
        TranslatorInterface $translator
    )    
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/invamount')
                                           ->withLayout(dirname(dirname(__DIR__)) .'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->userService = $userService;
        $this->invamountService = $invamountService;
        $this->translator = $translator;
    }
    
    public function index(SessionInterface $session, InvAmountRepository $invamountRepository, SettingRepository $settingRepository, Request $request): Response
    {
         $canEdit = $this->rbac($session);
         $flash = $this->flash($session, '','');
         $parameters = [
      
          's'=>$settingRepository,
          'canEdit' => $canEdit,
          'invamounts' => $this->invamounts($invamountRepository),
          'flash'=> $flash
         ];
        return $this->viewRenderer->render('index', $parameters);
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
            'action' => ['invamount/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$settingRepository,
            'head'=>$head,
            'invs'=>$invRepository->findAllPreloaded(),
        ];
        
        if ($request->getMethod() === Method::POST) {
            
            $form = new InvAmountForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->invamountService->saveInvAmount(new InvAmount(),$form);
                return $this->webService->getRedirectResponse('quoteamount/index');
            }
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function edit(ViewRenderer $head, SessionInterface $session, Request $request, CurrentRoute $currentRoute,
                        ValidatorInterface $validator,
                        InvAmountRepository $invamountRepository, 
                        SettingRepository $settingRepository,                        
                        InvRepository $invRepository
    ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => 'Edit',
            'action' => ['invamount/edit', ['id' => $this->invamount($currentRoute, $invamountRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->invamount($currentRoute, $invamountRepository)),
            'head'=>$head,
            's'=>$settingRepository,
            'invs'=>$invRepository->findAllPreloaded()
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new InvAmountForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->invamountService->saveInvAmount($this->invamount($currentRoute, $invamountRepository), $form);
                return $this->webService->getRedirectResponse('invamount/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function delete(SessionInterface $session, CurrentRoute $currentRoute, InvAmountRepository $invamountRepository 
    ): Response {
        $this->rbac($session);       
        $this->invamountService->deleteInvAmount($this->invamount($currentRoute, $invamountRepository));               
        return $this->webService->getRedirectResponse('invamount/index');        
    }
    
    public function view(SessionInterface $session, CurrentRoute $currentRoute, InvAmountRepository $invamountRepository,
        SettingRepository $settingRepository,
        ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('view'),
            'action' => ['invamount/edit', ['id' => $this->invamount($currentRoute, $invamountRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->quoteamount($currentRoute, $invamountRepository)),
            's'=>$settingRepository,             
            'quoteamount'=>$invamountRepository->repoInvAmountquery($this->invamount($currentRoute, $invamountRepository)->getId()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }
    
    private function rbac(SessionInterface $session) 
    {
        $canEdit = $this->userService->hasPermission('editInvAmount');
        if (!$canEdit){
            $this->flash($session,'warning', $this->translator->translate('invoice.permission'));
            return $this->webService->getRedirectResponse('invamount/index');
        }
        return $canEdit;
    }
    
    private function invamount(CurrentRoute $currentRoute, InvAmountRepository $invamountRepository) 
    {
        $id = $currentRoute->getArgument('id');       
        $invamount = $invamountRepository->repoInvAmountquery($id);
        if ($invamount === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $invamount;
    }
    
    private function invamounts(InvAmountRepository $invamountRepository) 
    {
        $invamounts = $invamountRepository->findAllPreloaded();        
        if ($invamounts === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $invamounts;
    }
    
    private function body($invamount) {
        $body = [
                
          'id'=>$invamount->getId(),
          'inv_id'=>$invamount->getInv_id(),
          'item_subtotal'=>$invamount->getItem_subtotal(),
          'item_tax_total'=>$invamount->getItem_tax_total(),
          'tax_total'=>$invamount->getTax_total(),
          'total'=>$invamount->getTotal()
        ];
        return $body;
    }
    
    private function flash(SessionInterface $session, $level, $message){
        $flash = new Flash($session);
        $flash->set($level, $message); 
        return $flash;
    }
}