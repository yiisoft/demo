<?php

declare(strict_types=1); 

namespace App\Invoice\QuoteItem;

use App\Invoice\Entity\QuoteItem;
use App\Invoice\QuoteItem\QuoteItemService;
use App\Invoice\QuoteItem\QuoteItemRepository;
use App\Invoice\Setting\SettingRepository;
use App\Invoice\TaxRate\TaxRateRepository;
use App\Invoice\Product\ProductRepository;
use App\Invoice\Quote\QuoteRepository;
use App\Invoice\Unit\UnitRepository;
use App\User\UserService;
use Yiisoft\Validator\ValidatorInterface;
use App\Service\WebControllerService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Http\Method;
use Yiisoft\Yii\View\ViewRenderer;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Session\Flash\Flash;

final class QuoteItemController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService;
    private QuoteItemService $quoteitemService;
    
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UserService $userService,
        QuoteItemService $quoteitemService
    )    
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/quoteitem')
                                           ->withLayout(dirname(dirname(__DIR__)) .'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->userService = $userService;
        $this->quoteitemService = $quoteitemService;
    }
    
    public function index(SessionInterface $session, QuoteItemRepository $quoteitemRepository, SettingRepository $settingRepository, Request $request, QuoteItemService $service): Response
    {
       
         $canEdit = $this->rbac($session);
         $flash = $this->flash($session, 'dummy' , 'Flash message!.');
         $parameters = [
      
          's'=>$settingRepository,
          'canEdit' => $canEdit,
          'quoteitems' => $this->quoteitems($quoteitemRepository),
          'flash'=> $flash
         ];

        if ($this->isAjaxRequest($request)) {
            return $this->viewRenderer->renderPartial('_quoteitems', ['data' => $paginator]);
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
                        TaxRateRepository $tax_rateRepository,
                        ProductRepository $productRepository,
                        UnitRepository $unitRepository,
                        QuoteRepository $quoteRepository
    )
    {
        $this->rbac($session);
        $parameters = [
            'title' => 'Add',
            'action' => ['quoteitem/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$settingRepository,
            'head'=>$head,
            'tax_rates'=>$tax_rateRepository->findAllPreloaded(),
            'products'=>$productRepository->findAllPreloaded(),
            'quotes'=>$quoteRepository->findAllPreloaded(),
            'units'=>$unitRepository->findAllPreloaded(),
        ];
        
        if ($request->getMethod() === Method::POST) {
            
            $form = new QuoteItemForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->quoteitemService->saveQuoteItem(new QuoteItem(),$form);
                return $this->webService->getRedirectResponse('quoteitem/index');
            }
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function edit(ViewRenderer $head, SessionInterface $session, Request $request, 
                        ValidatorInterface $validator,
                        QuoteItemRepository $quoteitemRepository, 
                        SettingRepository $settingRepository,                        
                        TaxRateRepository $tax_rateRepository,
                        ProductRepository $productRepository,                        
                        UnitRepository $unitRepository,
                        QuoteRepository $quoteRepository
    ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => 'Edit',
            'action' => ['quoteitem/edit', ['id' => $this->quoteitem($request, $quoteitemRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->quoteitem($request, $quoteitemRepository)),
            'head'=>$head,
            's'=>$settingRepository,
            'tax_rates'=>$tax_rateRepository->findAllPreloaded(),
            'products'=>$productRepository->findAllPreloaded(),
            'quotes'=>$quoteRepository->findAllPreloaded(),            
            'units'=>$unitRepository->findAllPreloaded(),
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new QuoteItemForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->quoteitemService->saveQuoteItem($this->quoteitem($request,$quoteitemRepository), $form);
                return $this->webService->getRedirectResponse('quoteitem/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function delete(SessionInterface $session,Request $request,QuoteItemRepository $quoteitemRepository 
    ): Response {
        $this->rbac($session);
       
        $this->quoteitemService->deleteQuoteItem($this->quoteitem($request,$quoteitemRepository));               
        return $this->webService->getRedirectResponse('quoteitem/index');        
    }
    
    public function view(SessionInterface $session,Request $request,QuoteItemRepository $quoteitemRepository,
        SettingRepository $settingRepository
        ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('view'),
            'action' => ['quoteitem/edit', ['id' => $this->quoteitem($request, $quoteitemRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->quoteitem($request, $quoteitemRepository)),
            's'=>$settingRepository,             
            'quoteitem'=>$quoteitemRepository->repoQuoteItemquery($this->quoteitem($request, $quoteitemRepository)->getId()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }
    
    private function rbac(SessionInterface $session) 
    {
        $canEdit = $this->userService->hasPermission('editQuoteItem');
        if (!$canEdit){
            $this->flash($session,'warning', 'You do not have the required permission.');
            return $this->webService->getRedirectResponse('quoteitem/index');
        }
        return $canEdit;
    }
    
    private function quoteitem(Request $request,QuoteItemRepository $quoteitemRepository) 
    {
        $id = $request->getAttribute('id');       
        $quoteitem = $quoteitemRepository->repoQuoteItemquery($id);
        if ($quoteitem === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $quoteitem;
    }
    
    private function quoteitems(QuoteItemRepository $quoteitemRepository) 
    {
        $quoteitems = $quoteitemRepository->findAllPreloaded();        
        if ($quoteitems === null) {
            return $this->webService->getNotFoundResponse();
        };
        return $quoteitems;
    }
    
    private function body($quoteitem) {
        $body = [
          'id'=>$quoteitem->getId(),
          'quote_id'=>$quoteitem->getQuote_id(),
          'tax_rate_id'=>$quoteitem->getTax_rate_id(),
          'product_id'=>$quoteitem->getProduct_id(),
          'date_added'=>$quoteitem->getDate_added(),
          'name'=>$quoteitem->getName(),
          'description'=>$quoteitem->getDescription(),
          'quantity'=>$quoteitem->getQuantity(),
          'price'=>$quoteitem->getPrice(),
          'discount_amount'=>$quoteitem->getDiscount_amount(),
          'order'=>$quoteitem->getOrder(),
          'product_unit'=>$quoteitem->getProduct_unit(),
          'product_unit_id'=>$quoteitem->getProduct_unit_id()
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