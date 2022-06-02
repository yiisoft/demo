<?php

declare(strict_types=1); 

namespace App\Invoice\QuoteAmount;

use App\Invoice\Entity\QuoteAmount;
use App\Invoice\QuoteAmount\QuoteAmountService;
use App\Invoice\QuoteAmount\QuoteAmountRepository;
use App\Invoice\Setting\SettingRepository;
use App\Invoice\Quote\QuoteRepository;

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

final class QuoteAmountController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService;
    private QuoteAmountService $quoteamountService;
    private TranslatorInterface $translator;
    
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UserService $userService,
        QuoteAmountService $quoteamountService,
        TranslatorInterface $translator,
    )    
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/quoteamount')
                                           ->withLayout(dirname(dirname(__DIR__)) .'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->userService = $userService;
        $this->quoteamountService = $quoteamountService;
        $this->translator = $translator;
    }
    
    public function index(SessionInterface $session, QuoteAmountRepository $quoteamountRepository, SettingRepository $settingRepository, Request $request, QuoteAmountService $service): Response
    {
       
         $canEdit = $this->rbac($session);
         $flash = $this->flash($session, '','');
         $parameters = [
      
          's'=>$settingRepository,
          'canEdit' => $canEdit,
          'quoteamounts' => $this->quoteamounts($quoteamountRepository),
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
                        QuoteRepository $quoteRepository
    )
    {
        $this->rbac($session);
        $parameters = [
            'title' => 'Add',
            'action' => ['quoteamount/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$settingRepository,
            'head'=>$head,
            
            'quotes'=>$quoteRepository->findAllPreloaded(),
        ];
        
        if ($request->getMethod() === Method::POST) {
            
            $form = new QuoteAmountForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->quoteamountService->saveQuoteAmount(new QuoteAmount(),$form);
                return $this->webService->getRedirectResponse('quoteamount/index');
            }
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function edit(ViewRenderer $head, SessionInterface $session, Request $request, CurrentRoute $currentRoute,
                        ValidatorInterface $validator,
                        QuoteAmountRepository $quoteamountRepository, 
                        SettingRepository $settingRepository,                        
                        QuoteRepository $quoteRepository
    ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => 'Edit',
            'action' => ['quoteamount/edit', ['id' => $this->quoteamount($currentRoute, $quoteamountRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->quoteamount($currentRoute, $quoteamountRepository)),
            'head'=>$head,
            's'=>$settingRepository,
            'quotes'=>$quoteRepository->findAllPreloaded()
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new QuoteAmountForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->quoteamountService->saveQuoteAmount($this->quoteamount($currentRoute, $quoteamountRepository), $form);
                return $this->webService->getRedirectResponse('quoteamount/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function delete(SessionInterface $session, CurrentRoute $currentRoute, QuoteAmountRepository $quoteamountRepository 
    ): Response {
        $this->rbac($session);       
        $this->quoteamountService->deleteQuoteAmount($this->quoteamount($currentRoute, $quoteamountRepository));               
        return $this->webService->getRedirectResponse('quoteamount/index');        
    }
    
    public function view(SessionInterface $session, CurrentRoute $currentRoute, QuoteAmountRepository $quoteamountRepository,
        SettingRepository $settingRepository,
        ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('view'),
            'action' => ['quoteamount/edit', ['id' => $this->quoteamount($currentRoute, $quoteamountRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->quoteamount($currentRoute, $quoteamountRepository)),
            's'=>$settingRepository,             
            'quoteamount'=>$quoteamountRepository->repoQuoteAmountquery($this->quoteamount($currentRoute, $quoteamountRepository)->getId()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }
    
    private function rbac(SessionInterface $session) 
    {
        $canEdit = $this->userService->hasPermission('editQuoteAmount');
        if (!$canEdit){
            $this->flash($session,'warning', $this->translator->translate('invoice.permission'));
            return $this->webService->getRedirectResponse('quoteamount/index');
        }
        return $canEdit;
    }
    
    private function quoteamount(CurrentRoute $currentRoute, QuoteAmountRepository $quoteamountRepository) 
    {
        $id = $currentRoute->getArgument('id');       
        $quoteamount = $quoteamountRepository->repoQuoteAmountquery($id);
        if ($quoteamount === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $quoteamount;
    }
    
    private function quoteamounts(QuoteAmountRepository $quoteamountRepository) 
    {
        $quoteamounts = $quoteamountRepository->findAllPreloaded();        
        if ($quoteamounts === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $quoteamounts;
    }
    
    private function body($quoteamount) {
        $body = [
                
          'id'=>$quoteamount->getId(),
          'quote_id'=>$quoteamount->getQuote_id(),
          'item_subtotal'=>$quoteamount->getItem_subtotal(),
          'item_tax_total'=>$quoteamount->getItem_tax_total(),
          'tax_total'=>$quoteamount->getTax_total(),
          'total'=>$quoteamount->getTotal()
        ];
        return $body;
    }
    
    private function flash(SessionInterface $session, $level, $message){
        $flash = new Flash($session);
        $flash->set($level, $message); 
        return $flash;
    }
}