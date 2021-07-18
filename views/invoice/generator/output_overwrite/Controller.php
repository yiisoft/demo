<?php

declare(strict_types=1); 

namespace App\Invoice\Product;

use App\Invoice\Entity\Product;
use App\Invoice\Product\ProductService;
use App\Invoice\Product\ProductRepository;
use App\Invoice\Setting\SettingRepository;
use App\Invoice\TaxRate\TaxRateRepository;
use App\Invoice\Family\FamilyRepository;
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

final class ProductController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService;
    private ProductService $productService;
    
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UserService $userService,
        ProductService $productService
    )    
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice\product')
                                           ->withLayout(dirname(dirname(__DIR__)) .' /Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->userService = $userService;
        $this->productService = $productService;
    }
    
    public function index(SessionInterface $session, ProductRepository $productRepository, SettingRepository $settingRepository, Request $request, ProductService $service): Response
    {
      
        $paginator = $service->getFeedPaginator();
        if ($request->getAttribute('') !== null) {
         $paginator = $paginator->withNextPageToken((string)$request->getAttribute('')); 
         $canEdit = $this->rbac($session);
         $flash = $this->flash($session, 'success' , 'Change the type from success to info and you will get a flash message!.');
         $parameters = [
      
          's'=>$settingRepository,
          'canEdit' => $canEdit,
          'products' => $this->products($productRepository),
          'flash'=> $flash
         ];
        }

        if ($this->isAjaxRequest($request)) {
            return $this->viewRenderer->renderPartial('_products', ['data' => $paginator]);
        }
        
        return $this->viewRenderer->render('index', $parameters);
    }
    
    private function isAjaxRequest(Request $request): bool
    {
        return $request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
    }
    
    public function add(ViewRenderer $head,SessionInterface $session, Request $request, 
                        ValidatorInterface $validator,
                        SettingRepository $SettingRepository,                        
                        TaxRateRepository $tax_rateRepository,
                        FamilyRepository $familyRepository,
                        UnitRepository $unitRepository
    )
    {
        $this->rbac($session);
        $parameters = [
            'title' => 'Add',
            'action' => ['products/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$SettingRepository,
            'head'=>$head,
            
            'tax_rates'=>$tax_rateRepository->findAllPreloaded(),
            'familys'=>$familyRepository->findAllPreloaded(),
            'units'=>$unitRepository->findAllPreloaded(),
        ];
        
        if ($request->getMethod() === Method::POST) {
            
            $form = new ProductForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->productService->saveProduct(new Product(),$form);
                return $this->webService->getRedirectResponse('invoice/product');
            }
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function edit(ViewRenderer $head, SessionInterface $session, Request $request, 
                        ValidatorInterface $validator,
                        ProductRepository $productRepository, 
                        SettingRepository $settingRepository,                        
                        TaxRateRepository $tax_rateRepository,
                        FamilyRepository $familyRepository,
                        UnitRepository $unitRepository
    ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => 'Edit',
            'action' => ['invoice/edit', ['id' => $this->product($request, $productRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->product($request, $productRepository)),
            'head'=>$head,
            's'=>$settingRepository,
            'head'=>$head,
                        'tax_rates'=>$tax_rateRepository->findAllPreloaded(),
            'familys'=>$familyRepository->findAllPreloaded(),
            'units'=>$unitRepository->findAllPreloaded()
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new ProductForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->productService->saveProduct($this->product($request,$productRepository), $form);
                return $this->webService->getRedirectResponse('product/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function delete(SessionInterface $session,Request $request,ProductRepository $productRepository 
    ): Response {
        $this->rbac($session);
        $this->flash($session, 'danger','This record has been deleted');
        $this->productService->deleteProduct($this->product($request,$productRepository));               
        return $this->webService->getRedirectResponse('product/index');        
    }
    
    public function view(SessionInterface $session,Request $request,ProductRepository $productRepository,
        SettingRepository $settingRepository,
        ValidatorInterface $validator
        ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('view'),
            'action' => ['invoice/edit', ['id' => $this->product($request, $productRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->product($request, $productRepository)),
            's'=>$settingRepository,
            //load Entity\Product BelongTo relations ie. $family, $tax_rate, $unit by means of repoProductQuery             
            'product'=>$productRepository->repoProductquery($this->product($request, $productRepository)->getId()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }
    
    private function rbac(SessionInterface $session) 
    {
        $canEdit = $this->userService->hasPermission('editProduct');
        if (!$canEdit){
            $this->flash($session,'warning', 'You do not have the required permission.');
            return $this->webService->getRedirectResponse('product/index');
        }
        return $canEdit;
    }
    
    private function product(Request $request,ProductRepository $productRepository) 
    {
        $id = $request->getAttribute('id');       
        $product = $productRepository->repoProductquery($id);
        if ($product === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $product;
    }
    
    private function products(ProductRepository $productRepository) 
    {
        $products = $productRepository->findAllPreloaded();        
        if ($products === null) {
            return $this->webService->getNotFoundResponse();
        };
        return $products;
    }
    
    private function body($products) {
        $body = [
                
          'id'=>$products->getid(),
          'product_sku'=>$products->getproduct_sku(),
          'product_name'=>$products->getproduct_name(),
          'product_description'=>$products->getproduct_description(),
          'product_price'=>$products->getproduct_price(),
          'purchase_price'=>$products->getpurchase_price(),
          'provider_name'=>$products->getprovider_name(),
          'family_id'=>$products->getfamily_id(),
          'tax_rate_id'=>$products->gettax_rate_id(),
          'unit_id'=>$products->getunit_id(),
          'product_tariff'=>$products->getproduct_tariff()
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