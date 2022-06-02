<?php

declare(strict_types=1); 

namespace App\Invoice\QuoteItemAmount;

use App\Invoice\Entity\QuoteItemAmount;
use App\Invoice\QuoteItem\QuoteItemRepository;
use App\Invoice\QuoteItemAmount\QuoteItemAmountService;
use App\Invoice\QuoteItemAmount\QuoteItemAmountRepository;
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
use \Exception;

final class QuoteItemAmountController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService;
    private QuoteItemAmountService $quoteitemamountService;
    private TranslatorInterface $translator;
        
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UserService $userService,
        QuoteItemAmountService $quoteitemamountService,
        TranslatorInterface $translator
    )    
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/quoteitemamount')
                                           ->withLayout(dirname(dirname(__DIR__)).'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->userService = $userService;
        $this->quoteitemamountService = $quoteitemamountService;
        $this->translator = $translator;
    }
    
    public function index(SessionInterface $session, QuoteItemAmountRepository $quoteitemamountRepository, SettingRepository $settingRepository, Request $request, QuoteItemAmountService $service): Response
    {      
         $canEdit = $this->rbac($session);
         $flash = $this->flash($session, '','');
         $parameters = [
      
          's'=>$settingRepository,
          'canEdit' => $canEdit,
          'quoteitemamounts' => $this->quoteitemamounts($quoteitemamountRepository),
          'flash'=> $flash
         ];

        if ($this->isAjaxRequest($request)) {
            return $this->viewRenderer->renderPartial('_quoteitemamounts', ['data' => $paginator]);
        }
        
        return $this->viewRenderer->render('index', $parameters);
    }
    
    public function index_adv_paginator(SessionInterface $session, QuoteItemAmountRepository $quoteitemamountRepository, SettingRepository $settingRepository, Request $request, QuoteItemAmountService $service): Response
    {
                  
        $canEdit = $this->rbac($session);
        $flash = $this->flash($session, '','');
        $parameters = [        
               's'=>$settingRepository,
               'canEdit' => $canEdit,
               'quoteitemamounts' => $this->quoteitemamounts($quoteitemamountRepository),
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
                        QuoteItemRepository $quote_itemRepository
    )
    {
        $this->rbac($session);
        $parameters = [
            'title' => 'Add',
            'action' => ['quoteitemamount/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$settingRepository,
            'head'=>$head,
            
            'quote_items'=>$quote_itemRepository->findAllPreloaded(),
        ];
        
        if ($request->getMethod() === Method::POST) {
            
            $form = new QuoteItemAmountForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->quoteitemamountService->saveQuoteItemAmount(new QuoteItemAmount(),$form);
                return $this->webService->getRedirectResponse('quoteitemamount/index');
            }
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function edit(ViewRenderer $head, SessionInterface $session, Request $request, CurrentRoute $currentRoute, 
                        ValidatorInterface $validator,
                        QuoteItemAmountRepository $quoteitemamountRepository, 
                        SettingRepository $settingRepository,                        
                        QuoteItemRepository $quote_itemRepository
    ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => 'Edit',
            'action' => ['quoteitemamount/edit', ['id' => $this->quoteitemamount($currentRoute, $quoteitemamountRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->quoteitemamount($currentRoute, $quoteitemamountRepository)),
            'head'=>$head,
            's'=>$settingRepository,
                        'quote_items'=>$quote_itemRepository->findAllPreloaded()
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new QuoteItemAmountForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->quoteitemamountService->saveQuoteItemAmount($this->quoteitemamount($currentRoute,$quoteitemamountRepository), $form);
                return $this->webService->getRedirectResponse('quoteitemamount/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function delete(SessionInterface $session, CurrentRoute $currentRoute, QuoteItemAmountRepository $quoteitemamountRepository 
    ): Response {
        $this->rbac($session);
        try {
            $this->quoteitemamountService->deleteQuoteItemAmount($this->quoteitemamount($currentRoute, $quoteitemamountRepository));               
            return $this->webService->getRedirectResponse('quoteitemamount/index'); 
	} catch (Exception $e) {
            //unset($e);
            $this->flash($session, 'danger', $e);
            return $this->webService->getRedirectResponse('quoteitemamount/index'); 
        }
    }
    
    public function view(SessionInterface $session, CurrentRoute $currentRoute, QuoteItemAmountRepository $quoteitemamountRepository,
        SettingRepository $settingRepository,
        ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('view'),
            'action' => ['quoteitemamount/view', ['id' => $this->quoteitemamount($currentRoute, $quoteitemamountRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->quoteitemamount($currentRoute, $quoteitemamountRepository)),
            's'=>$settingRepository,             
            'quoteitemamount'=>$quoteitemamountRepository->repoQuoteItemAmountquery($this->quoteitemamount($currentRoute, $quoteitemamountRepository)->getId()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }   
        
    private function rbac(SessionInterface $session) 
    {
        $canEdit = $this->userService->hasPermission('editQuoteItemAmount');
        if (!$canEdit){
            $this->flash($session,'warning', $this->translator->translate('invoice.permission'));
            return $this->webService->getRedirectResponse('quoteitemamount/index');
        }
        return $canEdit;
    }
    
    private function quoteitemamount(CurrentRoute $currentRoute, QuoteItemAmountRepository $quoteitemamountRepository) 
    {
        $id = $currentRoute->getAttribute('id');       
        $quoteitemamount = $quoteitemamountRepository->repoQuoteItemAmountquery($id);
        if ($quoteitemamount === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $quoteitemamount;
    }
    
    private function quoteitemamounts(QuoteItemAmountRepository $quoteitemamountRepository) 
    {
        $quoteitemamounts = $quoteitemamountRepository->findAllPreloaded();        
        if ($quoteitemamounts === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $quoteitemamounts;
    }
    
    private function body($quoteitemamount) {
        $body = [
          'id'=>$quoteitemamount->getId(),
          'quote_item_id'=>$quoteitemamount->getQuote_item_id(),
          'subtotal'=>$quoteitemamount->getSubtotal(),
          'tax_total'=>$quoteitemamount->getTax_total(),
          'discount'=>$quoteitemamount->getDiscount(),
          'total'=>$quoteitemamount->getTotal()
        ];
        return $body;
    }
    
    private function flash(SessionInterface $session, $level, $message){
        $flash = new Flash($session);
        $flash->set($level, $message); 
        return $flash;
    }
}
