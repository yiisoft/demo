<?php
declare(strict_types=1);

namespace App\Invoice\Product;

use App\Invoice\Entity\Product;
use App\Invoice\Entity\QuoteItem;
use App\Invoice\Entity\InvItem;
use App\Invoice\Family\FamilyRepository as fR;
use App\Invoice\Helpers\NumberHelper;
// Product
use App\Invoice\Product\ProductService;
use App\Invoice\Product\ProductRepository as pR;
// Quote
use App\Invoice\QuoteItem\QuoteItemForm;
use App\Invoice\QuoteItem\QuoteItemService;
use App\Invoice\QuoteItemAmount\QuoteItemAmountService as qiaS;
// Inv
use App\Invoice\InvItem\InvItemForm;
use App\Invoice\InvItem\InvItemService;
use App\Invoice\InvItemAmount\InvItemAmountService as iiaS;
// Setting, TaxRate, Unit
use App\Invoice\Setting\SettingRepository as sR;
use App\Invoice\TaxRate\TaxRateRepository as trR;
use App\Invoice\Unit\UnitRepository as uR;
use App\Invoice\QuoteItem\QuoteItemRepository as qiR;
use App\Invoice\InvItem\InvItemRepository as iiR;
use App\Invoice\QuoteItemAmount\QuoteItemAmountRepository as qiaR;
use App\Invoice\QuoteTaxRate\QuoteTaxRateRepository as qtrR;
use App\Invoice\InvTaxRate\InvTaxRateRepository as itrR;
use App\Invoice\QuoteAmount\QuoteAmountRepository as qaR;
use App\Invoice\InvAmount\InvAmountRepository as iaR;
use App\Invoice\Quote\QuoteRepository as qR;
use App\Invoice\Inv\InvRepository as iR;
use App\Invoice\InvItemAmount\InvItemAmountRepository as iiaR;
use App\Invoice\Payment\PaymentRepository as pymR;
use App\Service\WebControllerService;
use App\User\UserService;

//  Psr
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
// Yiisoft
use Yiisoft\DataResponse\DataResponseFactoryInterface;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Http\Method;
use Yiisoft\Json\Json;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Session\Flash\Flash;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\View\ViewRenderer;

use DateTimeImmutable;
use \Exception;

class ProductController
{
    private const FILTER_FAMILY = 'ff';
    private const FILTER_PRODUCT = 'fp';
    private const RESET_TRUE = 'rt';
    public  ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private ProductService $productService;
    private UserService $userService;   
    private DataResponseFactoryInterface $responseFactory;
    private SessionInterface $session;
    private TranslatorInterface $translator;
    private string $ffc = self::FILTER_FAMILY;
    private string $fpc = self::FILTER_PRODUCT;
    private string $rtc = self::RESET_TRUE;
    
    public function __construct(
            ViewRenderer $viewRenderer,
            WebControllerService $webService,
            ProductService $productService,
            QuoteItemService $quoteitemService,
            InvItemService $invitemService,
            UserService $userService,
            DataResponseFactoryInterface $responseFactory,
            SessionInterface $session,
            TranslatorInterface $translator
    )
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/product')
                                           ->withLayout(dirname(dirname(__DIR__)).'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->productService = $productService;
        $this->quoteitemService = $quoteitemService;
        $this->invitemService = $invitemService;
        $this->userService = $userService;
        $this->responseFactory = $responseFactory;
        $this->session = $session;
        $this->translator = $translator;
    }
    
    public function add(ViewRenderer $head, Request $request, ValidatorInterface $validator, sR $sR, fR $fR, uR $uR, trR $trR): Response
    {
        $this->rbac();
        $parameters = [
            'title' => $sR->trans('add'),
            'action' => ['product/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$sR,
            'head'=>$head,
            'families'=>$fR->findAllPreloaded(),
            'units'=>$uR->findAllPreloaded(),
            'tax_rates'=>$trR->findAllPreloaded()
        ];
        
        if ($request->getMethod() === Method::POST) {            
            $form = new ProductForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                try {  
                  $this->productService->saveProduct(new Product(),$form);
                  $this->flash('info', $sR->trans('record_successfully_added'));
                } catch (Exception $e){
                    switch ($e->getCode()) {
                        //catch integrity constraint on tax_rate_id => 23000
                        case 23000 :
                           $message = 'Incomplete fields';
                           break;
                        default : 
                           $message = 'Unknown error.';
                           break;
                    }   
                    $this->flash('danger', $message . ' ' . $e->getCode());
                    unset($e);   
                }
                return $this->webService->getRedirectResponse('product/index');   
            }  
            $parameters['errors'] = $form->getFormErrors();
        }
        
        if ($this->isAjaxRequest($request)){
                return $this->viewRenderer->renderPartial('_form', $parameters);                
        } else {
                return $this->viewRenderer->render('_form', $parameters);
        }
    }
    
    private function body($product) {
        $body = [
                'id'=>$product->getProduct_id(),
                'product_sku'=>$product->getProduct_sku(),
                'product_name'=>$product->getProduct_name(),
                'product_description' => $product->getProduct_description(),
                'product_price' => $product->getProduct_price(),
                'purchase_price' => $product->getPurchase_price(),
                'provider_name' => $product->getProvider_name(),
                'tax_rate_id'=>$product->getTax_rate_id(),
                'unit_id'=>$product->getUnit_id(),
                'family_id'=>$product->getFamily_id(), 
                'product_tariff'=>$product->getProduct_tariff()
                ];
        return $body;
    }
    
    public function delete(pR $pR, CurrentRoute $currentRoute
    ): Response {
        $this->rbac();
        try {
            $this->productService->deleteProduct($this->product($currentRoute, $pR));               
            return $this->webService->getRedirectResponse('product/index');   
	} catch (Exception $e) {
           unset($e);
           $this->flash('danger', 'Cannot delete. This product is on an invoice or quote.');
           return $this->webService->getRedirectResponse('product/index');   
        }
    }
        
    private function flash($level, $message){
        $flash = new Flash($this->session);
        $flash->set($level, $message); 
        return $flash;
    }
    
    public function edit_with_catch(ViewRenderer $head, Request $request, CurrentRoute $currentRoute, ValidatorInterface $validator,
                    pR $pR, sR $sR, fR $fR, uR $uR, trR $trR, 
    ): Response {
        $this->rbac();
        $parameters = [
            'title' => $sR->trans('edit'),
            'action' => ['product/edit', ['id' => $this->product($currentRoute, $pR)->getProduct_id()]],
            'errors' => [],
            'body' => $this->body($this->product($currentRoute, $pR)),
            's'=>$sR,
            'head'=>$head,
            'families'=>$fR->findAllPreloaded(),
            'units'=>$uR->findAllPreloaded(),
            'tax_rates'=>$trR->findAllPreloaded()    
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new ProductForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                try {  
                    $this->productService->saveProduct($this->product($currentRoute, $pR), $form);                    
                } catch (Exception $e){
                    switch ($e->getCode()) {
                        //catch integrity constraint on tax_rate_id => 23000
                        case 23000 :
                           $message = 'Incomplete fields';
                           break;
                        case 0 : 
                            $message = '0';
                           break;
                        default : 
                           $message = 'Default';
                           break;
                    }   
                    $this->flash('danger', $message . ' ' . $e->getCode());
                    unset($e);   
                }
                return $this->webService->getRedirectResponse('product/index');   
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function edit(ViewRenderer $head, Request $request, CurrentRoute $currentRoute, ValidatorInterface $validator,
                    pR $pR, sR $sR, fR $fR, uR $uR, trR $trR, 
    ): Response {
        $this->rbac();
        $parameters = [
            'title' => $sR->trans('edit'),
            'action' => ['product/edit', ['id' => $this->product($currentRoute, $pR)->getProduct_id()]],
            'errors' => [],
            'body' => $this->body($this->product($currentRoute, $pR)),
            's'=>$sR,
            'head'=>$head,
            'families'=>$fR->findAllPreloaded(),
            'units'=>$uR->findAllPreloaded(),
            'tax_rates'=>$trR->findAllPreloaded()    
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new ProductForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->productService->saveProduct($this->product($currentRoute, $pR), $form);                    
                return $this->webService->getRedirectResponse('product/index');   
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    private function isAjaxRequest(Request $request): bool
    {
        return $request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
    }

    public function index(pR $pR, sR $sR, CurrentRoute $currentRoute, Request $request): Response
    {
        $canEdit = $this->rbac(); 
        $flash = $this->flash('success', 'Help information will appear here.'); 
        $pageNum = (int)$currentRoute->getArgument('page', '1');
        $paginator = (new OffsetPaginator($this->products($pR)))
            ->withPageSize((int)$sR->setting('default_list_limit'))
            ->withCurrentPage($pageNum);
        $parameters = [
            'paginator'=>$paginator,
            's'=> $sR,
            'canEdit' => $canEdit,
            'products' => $this->products($pR), 
            'flash'=> $flash
        ];        
        if ($this->isAjaxRequest($request)) {
            return $this->viewRenderer->renderPartial('_products', ['data' => $paginator]);
        }
        return $this->viewRenderer->render('index', $parameters);
    }
    
    // queryparams coming from modal_product_lookups.js ---> line 165 filter_button_inv
    public function lookup(ViewRenderer $head,Request $request, fR $fR, sR $sR, pR $pR): Response {
        $this->rbac();
        $queryparams = $request->getQueryParams() ?? [];
        $fp = $queryparams[$this->fpc] ?? '';
        $ff = $queryparams[$this->ffc] ?? '';
        $rt = $queryparams[$this->rtc] ?? '';
        $parameters = [
            'numberhelper'=>new NumberHelper($sR),
            'families'=> $fR->findAllPreloaded(),
            'filter_product'=> $fp,            
            'filter_family'=> $ff,
            'reset_table'=> $rt,
            's'=> $sR,
            'head'=> $head,
            'products'=> $rt || ($ff=='' && $fp=='') ? $pR->findAllPreloaded() : $pR->repoProductwithfamilyquery($fp, $ff),
            'default_item_tax_rate'=> $sR->get_setting('default_item_tax_rate') !== '' ?: 0,
        ];
        
        if ($this->isAjaxRequest($request)){
            //url filters being used and reset unused
            if ($fp || $ff || ($ff=='' && $fp=='')) {
                return $this->viewRenderer->renderPartial('_partial_product_table_modal', $parameters);    
            } 
        }
    }
    
     private function save_product_lookup_item_quote($order, $product, $quote_id, $pR, $trR, $unR, $qiaR, $uR, ValidatorInterface $validator) : void {
           $form = new QuoteItemForm();
           $ajax_content = [
                'name'=>$product->getProduct_name(),        
                'quote_id'=>$quote_id,            
                'tax_rate_id'=>$product->getTax_rate_id(),
                'product_id'=>$product->getProduct_id(),
                'date_added'=>new DateTimeImmutable(),
                'description'=>$product->getProduct_description(),
                // A default quantity of 1 is used to initialize the item
                'quantity'=>floatval(1),
                'price'=>$product->getProduct_price(),
                // The user will determine how much discount to give on this item later
                'discount_amount'=>floatval(0),
                'order'=>$order,
                // The default quantity is 1 so the singular name will be used.
                'product_unit'=>$unR->singular_or_plural_name((string)$product->getUnit_id(),1),
                'product_unit_id'=>(string)$product->getUnit_id(),
           ];
           if ($form->load($ajax_content) && $validator->validate($form)->isValid()) {
                 $this->quoteitemService->saveQuoteItem(new QuoteItem(), $form, $quote_id, $pR, $trR, new qiaS($qiaR),$qiaR, $uR);                 
           }      
    }
    
      private function save_product_lookup_item_inv($order, $product, $inv_id, $pR, $trR, $unR, $iiaR, $uR, ValidatorInterface $validator) : void {
           $form = new InvItemForm();
           $ajax_content = [
                'name'=>$product->getProduct_name(),        
                'inv_id'=>$inv_id,            
                'tax_rate_id'=>$product->getTax_rate_id(),
                'product_id'=>$product->getProduct_id(),
                'date_added'=>new DateTimeImmutable(),
                'description'=>$product->getProduct_description(),
                // A default quantity of 1 is used to initialize the item
                'quantity'=>floatval(1),
                'price'=>$product->getProduct_price(),
                // The user will determine how much discount to give on this item later
                'discount_amount'=>floatval(0),
                'order'=>$order,
                // The default quantity is 1 so the singular name will be used.
                'product_unit'=>$unR->singular_or_plural_name((string)$product->getUnit_id(),1),
                'product_unit_id'=>(string)$product->getUnit_id(),
           ];
           if ($form->load($ajax_content) && $validator->validate($form)->isValid()) {
                $this->invitemService->saveInvItem(new InvItem(), $form, $inv_id, $pR, $trR, new iiaS($iiaR),$iiaR, $uR);                 
           }      
    }
    
    //views/invoice/product/modal-product-lookups-quote.php => modal_product_lookups.js $(document).on('click', '.select-items-confirm-quote', function () => selection_quote
    public function selection_quote(ValidatorInterface $validator, Request $request,
                                   pR $pR, qaR $qaR, qiR $qiR, qR $qR, qtrR $qtrR,
                                   sR $sR, trR $trR, uR $uR, qiaR $qiaR) : Response {        
        try {
        $this->rbac();
        $select_items = $request->getQueryParams() ?? [];
        $product_ids = ($select_items['product_ids'] ? $select_items['product_ids'] : []);
        $quote_id = $select_items['quote_id'];
        // Use Spiral||Cycle\Database\Injection\Parameter to build 'IN' array of products.
        $products = $pR->findinProducts($product_ids);
        $numberHelper = new NumberHelper($sR);
        // Format the product prices according to comma or point or other setting choice.
        $order = 1;
        foreach ($products as $product) {
            $product->setProduct_price((float)$numberHelper->format_amount($product->getProduct_price()));
            $this->save_product_lookup_item_quote($order, $product, $quote_id, $pR, $trR, $uR, $qiaR, $uR,$validator);
            $order++;          
        } 
        $numberHelper->calculate_quote($this->session->get('quote_id'), $qiR, $qiaR, $qtrR, $qaR, $qR); 
        if ($this->isAjaxRequest($request)){
          return $this->responseFactory->createResponse(Json::encode($products));
        } else {
          //testing DOM link
          return $this->responseFactory->createResponse(Json::encode($products)); 
        }
        } catch (Exception $e){
           // unset($e);
            $this->flash('danger', 'Unselected Product'.$e);
             return $this->responseFactory->createResponse(Json::encode($products));             
        }
    }
    
    //views/invoice/product/modal-product-lookups-inv.php => modal_product_lookups.js $(document).on('click', '.select-items-confirm-inv', function () 
    public function selection_inv(ValidatorInterface $validator, Request $request, pR $pR, sR $sR, trR $trR, uR $uR, iiaR $iiaR, iiR $iiR, itrR $itrR, iaR $iaR, iR $iR, pymR $pymR) : Response {        
        try {
        $this->rbac();
        $select_items = $request->getQueryParams() ?? [];
        $product_ids = ($select_items['product_ids'] ? $select_items['product_ids'] : []);
        $inv_id = $select_items['inv_id'];
        // Use Spiral||Cycle\Database\Injection\Parameter to build 'IN' array of products.
        $products = $pR->findinProducts($product_ids);
        $numberHelper = new NumberHelper($sR);
        // Format the product prices according to comma or point or other setting choice.
        $order = 1;
        foreach ($products as $product) {
            $product->setProduct_price((float)$numberHelper->format_amount($product->getProduct_price()));
            $this->save_product_lookup_item_inv($order, $product, $inv_id, $pR, $trR, $uR, $iiaR, $uR, $validator);
            $order++;          
        }
        
        $numberHelper->calculate_inv($this->session->get('inv_id'), $iiR, $iiaR, $itrR, $iaR, $iR, $pymR);
        if ($this->isAjaxRequest($request)){
          return $this->responseFactory->createResponse(Json::encode($products));
        } else {
          //testing DOM link
          return $this->responseFactory->createResponse(Json::encode($products)); 
        }
        } catch (Exception $e){
           // unset($e);
            $this->flash('danger', 'Unselected Product'.$e);
            return $this->responseFactory->createResponse(array_keys($products));             
        }
    }
    
    
    
    private function product(CurrentRoute $currentRoute, pR $pR) {        
        $id = $currentRoute->getArgument('id');
        $product = $pR->repoProductquery($id);
        if ($product === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $product;
    }
    
    private function products(pR $pR) {
        $products = $pR->findAllPreloaded();        
        if ($products === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $products;
    }
    
    private function rbac() {
        $canEdit = $this->userService->hasPermission('editProduct');
        if (!$canEdit){
            $this->flash('warning', $this->translator->translate('invoice.permission'));
            return $this->webService->getRedirectResponse('product/index');
        }
        return $canEdit;
    }
    
    public function view(pR $pR,sR $sR, CurrentRoute $currentRoute
    ): Response {
        $this->rbac();
        $parameters = [
            'title' => $sR->trans('view'),
            'action' => ['product/view', ['id' => $this->product($currentRoute,$pR)->getProduct_id()]],
            'errors' => [],
            'body' => $this->body($this->product($currentRoute,$pR)),
            's'=>$sR,
            //load Entity\Product BelongTo relations ie. $family, $tax_rate, $unit by means of repoProductQuery             
            'product'=>$pR->repoProductquery($this->product($currentRoute, $pR)->getProduct_id()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }
}
