<?php
declare(strict_types=1); 

namespace App\Invoice\QuoteItem;

use App\Invoice\Entity\QuoteItem;
use App\Invoice\Product\ProductRepository as PR; 
use App\Invoice\Quote\QuoteRepository as QR;
use App\Invoice\QuoteItem\QuoteItemService;
use App\Invoice\QuoteItem\QuoteItemForm;
use App\Invoice\QuoteItem\QuoteItemRepository as QIR;
use App\Invoice\QuoteItemAmount\QuoteItemAmountRepository as QIAR;
use App\Invoice\QuoteItemAmount\QuoteItemAmountService as QIAS;
use App\Invoice\Setting\SettingRepository as SR;
use App\Invoice\TaxRate\TaxRateRepository aS TRR;
use App\Invoice\Unit\UnitRepository as UR;
use App\Service\WebControllerService;
use App\User\UserService;
// Helpers
use App\Invoice\Helpers\NumberHelper;
// Psr
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
// Yii
use Yiisoft\DataResponse\DataResponseFactoryInterface;
use Yiisoft\Http\Method;
use Yiisoft\Json\Json;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Router\FastRoute\UrlGenerator;
use Yiisoft\Session\Flash\Flash;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\View\ViewRenderer;
use \Exception;

final class QuoteItemController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService;
    private QuoteItemService $quoteitemService;    
    private DataResponseFactoryInterface $factory;
    private UrlGenerator $urlGenerator;
    private TranslatorInterface $translator;
    
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UserService $userService,
        QuoteItemService $quoteitemService,        
        DataResponseFactoryInterface $factory,
        UrlGenerator $urlGenerator,
        TranslatorInterface $translator,
    )    
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/quoteitem')
                                           ->withLayout(dirname(dirname(__DIR__)) .'/Invoice/Layout/main.php');                                                
        $this->webService = $webService;
        $this->userService = $userService;
        $this->quoteitemService = $quoteitemService;
        $this->factory = $factory;
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
    }
    
    public function index(SessionInterface $session, QIR $qiR, SR $sR): Response
    {       
         $canEdit = $this->rbac($session);
         $flash = $this->flash($session, '','');
         $parameters = [      
          's'=>$sR,
          'quote_id'=>$session->get('quote_id'),
          'canEdit' => $canEdit,
          'quoteitems' => $this->quoteitems($qiR),
          'flash'=> $flash
         ];
        
        return $this->viewRenderer->render('index', $parameters);
    }
    
    // Quoteitem/add accessed from quote/view renderpartialasstring add_quote_item
    public function add(ViewRenderer $head,SessionInterface $session, Request $request,  
                        ValidatorInterface $validator,
                        SR $sR,
                        PR $pR,
                        UR $uR,                                                
                        TRR $trR,
                        QIAR $qiar,
    ) : Response
    {
        $quote_id = $session->get('quote_id');
        $this->rbac($session);
        $parameters = [
            'title' => 'Add',
            'action' => ['quoteitem/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$sR,
            'head'=>$head,
            'quote_id'=>$quote_id,
            'tax_rates'=>$trR->findAllPreloaded(),
            'products'=>$pR->findAllPreloaded(),
            'units'=>$uR->findAllPreloaded(),
            'numberhelper'=>new NumberHelper($sR)
        ];
        
        if ($request->getMethod() === Method::POST) {            
           $form = new QuoteItemForm();
           if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                try {  
                  $this->quoteitemService->saveQuoteItem(new QuoteItem(), $form, $quote_id, $pR, $trR, new QIAS($qiar),$qiar, $uR);
                  $this->flash($session, 'info', $sR->trans('record_successfully_added'));
                  return $this->factory->createResponse($this->viewRenderer->renderPartialAsString('/invoice/setting/quote_successful',
                         ['heading'=>'Successful','_language'=>'en',
                          'message'=>$sR->trans('record_successfully_added'),'url'=>'quote/view','id'=>$quote_id]));  
                } catch (Exception $e){
                    switch ($e->getCode()) {
                        //catch integrity constraint on tax_rate_id => 23000
                        case 23000 :
                           $message = 'Incomplete fields: All required fields must be filled. If you require no tax rate, create a zero tax rate.';
                           break;
                        default : 
                           $message = 'Unknown error.';
                           break;
                    }   
                    $this->flash($session, 'danger', $message . ' ' . $e->getCode());
                    unset($e);   
                }
                return $this->factory->createResponse($this->viewRenderer->renderPartialAsString('/invoice/setting/successful',
         ['heading'=>'Not successful. '.$message,'_language'=>'en','message'=>$sR->trans('record_successfully_added'),'url'=>'quote/view','id'=>$quote_id]));  
            }
            $parameters['errors'] = $form->getFormErrors();
        }
        
        if ($this->isAjaxRequest($request)) {
            return $this->viewRenderer->renderPartial('_item_form', $parameters);
        }
        else {
            return $this->viewRenderer->render('_item_form', $parameters);
        }
    }
       
    public function edit(ViewRenderer $head, SessionInterface $session, CurrentRoute $currentRoute, Request $request, ValidatorInterface $validator,
                        QIR $qiR, SR $sR, TRR $trR, PR $pR, UR $uR, QR $qR, QIAS $qias, QIAR $qiar): Response {
        $this->rbac($session);
        $quote_id = $session->get('quote_id');
        $parameters = [
            'title' => 'Edit',
            'action' => ['quoteitem/edit', ['id' => $this->quoteitem($currentRoute, $qiR)->getId()]],
            'errors' => [],
            'body' => $this->body($this->quoteitem($currentRoute, $qiR)),
            'quote_id'=>$quote_id,
            'head'=>$head,
            's'=>$sR,
            'tax_rates'=>$trR->findAllPreloaded(),
            'products'=>$pR->findAllPreloaded(),
            'quotes'=>$qR->findAllPreloaded(),            
            'units'=>$uR->findAllPreloaded(),
            'numberhelper'=>new NumberHelper($sR)
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new QuoteItemForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
            try {    
                $this->quoteitemService->saveQuoteItem($this->quoteitem($currentRoute, $qiR), $form, $quote_id, $pR, $trR , $qias, $qiar, $uR);
                 return $this->factory->createResponse($this->viewRenderer->renderPartialAsString('/invoice/setting/quote_successful',
                 ['heading'=>'Successful','message'=>$sR->trans('record_successfully_updated'),'url'=>'quote/view','id'=>$quote_id])); 
                } catch (Exception $e){
                    switch ($e->getCode()) {
                        //catch integrity constraint on tax_rate_id => 23000
                        case 23000 :
                           $message = 'Incomplete fields: All required fields must be filled. If you require no tax rate, create a zero tax rate.';
                           break;
                        default : 
                           $message = 'Incomplete fields.';
                           break;
                    }   
                    $this->flash($session, 'danger', $message . ' ' . $e->getCode());
                    unset($e);
                    return $this->factory->createResponse($this->viewRenderer->renderPartialAsString('/invoice/setting/successful',
                 ['heading'=>'Not successful','message'=>$message,'url'=>'quote/view','id'=>$quote_id])); 
                }
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFormErrors();
        } 
        return $this->viewRenderer->render('_item_edit_form', $parameters);
    }
    
    public function delete(CurrentRoute $currentRoute, QIR $qiR): Response {
        $qiR->repoQuoteItemCount($this->quoteitem($currentRoute, $qiR)->getId()) === 1  ? (($this->quoteitemService->deleteQuoteItem($this->quoteitem($currentRoute, $qiR)))): '';
        return $this->viewRenderer->render('quote/index');
    }
    
    public function multiple(Request $request, QIR $qiR): Response {
        //jQuery parameters from quote.js function delete-items-confirm-quote 'item_ids' and 'quote_id'
        $select_items = $request->getQueryParams() ?? [];
        $result = false;
        $item_ids = ($select_items['item_ids'] ? $select_items['item_ids'] : []);
        $items = $qiR->findinQuoteItems($item_ids);
        // If one item is deleted, the result is positive
        foreach ($items as $item){
            ($this->quoteitemService->deleteQuoteItem($item));
            $result = true;
        }
        return $this->factory->createResponse(Json::encode(($result ? ['success'=>1]:['success'=>0])));  
    }
    
    public function view(SessionInterface $session, CurrentRoute $currentRoute, QIR $qiR,
        SR $sR 
        ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $sR->trans('view'),
            'action' => ['quoteitem/edit', ['id' => $this->quoteitem($currentRoute, $qiR)->getId()]],
            'errors' => [],
            'body' => $this->body($this->quoteitem($currentRoute, $qiR)),
            's'=>$sR,             
            'quoteitem'=>$qiR->repoQuoteItemquery($this->quoteitem($currentRoute, $qiR)->getId()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }
    
    private function rbac(SessionInterface $session) 
    {
        $canEdit = $this->userService->hasPermission('editQuoteItem');
        if (!$canEdit){
            $this->flash($session,'warning', $this->translator->translate('invoice.permission'));
            return $this->webService->getRedirectResponse('quoteitem/index');
        }
        return $canEdit;
    }
    
    private function quoteitem(CurrentRoute $currentRoute, QIR $qiR) 
    {
        $id = $currentRoute->getArgument('id'); 
        $quoteitem = ($qiR->repoQuoteItemCount($id) === 1 ? $qiR->repoQuoteItemquery($id) : '');
        return $quoteitem;
    }
    
    private function quoteitems(QIR $qiR) 
    {
        $quoteitems = $qiR->findAllPreloaded();        
        if ($quoteitems === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $quoteitems;
    }
    
    private function body($quoteitem) {
        $body = [
          'id'=>$quoteitem->getId(),
          'quote_id'=>$quoteitem->getQuote_id(),
          'tax_rate_id'=>$quoteitem->getTax_rate_id(),
          'product_id'=>$quoteitem->getProduct_id(),
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