<?php

declare(strict_types=1);

namespace App\Invoice\Product;

use App\Invoice\Entity\Product;
use App\Invoice\Product\ProductService;
use App\Invoice\Setting\SettingRepository;
use App\Invoice\Product\ProductRepository;
use App\Invoice\Family\FamilyRepository;
use App\Invoice\Unit\UnitRepository;
use App\Invoice\TaxRate\TaxRateRepository;
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
    private ProductService $productService;
    private UserService $userService;    
    
    public function __construct(
            ViewRenderer $viewRenderer,
            WebControllerService $webService,
            ProductService $productService,
            UserService $userService
    )
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/product')
                                           ->withLayout(dirname(dirname(__DIR__)).'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->productService = $productService;
        $this->userService = $userService;
    }

    public function index(SessionInterface $session, ProductRepository $productRepository, SettingRepository $settingRepository, Request $request, ProductService $service): Response
    {
        $paginator = $service->getFeedPaginator();
        if ($request->getAttribute('next') !== null) {
            $paginator = $paginator->withNextPageToken((string)$request->getAttribute('next'));
        }
        $canEdit = $this->rbac($session); 
        $flash = $this->flash($session, 'success', 'Help information will appear here.');
        $parameters = [
            's'=> $settingRepository,
            'canEdit' => $canEdit,
            'products' => $this->products($productRepository), 
            'flash'=> $flash
        ];
        
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
                        ValidatorInterface $validator, SettingRepository $settingRepository, 
                        FamilyRepository $familyRepository, UnitRepository $unitRepository, TaxRateRepository $taxrateRepository): Response
    {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('add'),
            'action' => ['product/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$settingRepository,
            'head'=>$head,
            'families'=>$familyRepository->findAllPreloaded(),
            'units'=>$unitRepository->findAllPreloaded(),
            'tax_rates'=>$taxrateRepository->findAllPreloaded(),
        ];
        
        if ($request->getMethod() === Method::POST) {
            
            $form = new ProductForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->productService->saveProduct(new Product(),$form);
                return $this->webService->getRedirectResponse('product/index');
            }
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }

    public function edit(ViewRenderer $head, SessionInterface $session, Request $request, ProductRepository $productRepository, 
                         ValidatorInterface $validator, SettingRepository $settingRepository, FamilyRepository $familyRepository, 
                         UnitRepository $unitRepository, TaxRateRepository $taxrateRepository
    ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('edit'),
            'action' => ['product/edit', ['product_id' => $this->product($request, $productRepository)->getProduct_id()]],
            'errors' => [],
            'body' => $this->body($this->product($request, $productRepository)),
            's'=>$settingRepository,
            'head'=>$head,
            'families'=>$familyRepository->findAllPreloaded(),
            'units'=>$unitRepository->findAllPreloaded(),
            'tax_rates'=>$taxrateRepository->findAllPreloaded()    
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
       
        $this->productService->deleteProduct($this->product($request,$productRepository));               
        return $this->webService->getRedirectResponse('product/index');        
    }
    
    public function view(SessionInterface $session,Request $request,ProductRepository $productRepository,SettingRepository $settingRepository,
                         ValidatorInterface $validator
    ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('view'),
            'action' => ['product/edit', ['product_id' => $this->product($request, $productRepository)->getProduct_id()]],
            'errors' => [],
            'body' => $this->body($this->product($request, $productRepository)),
            's'=>$settingRepository,
            //load Entity\Product BelongTo relations ie. $family, $tax_rate, $unit by means of repoProductQuery             
            'product'=>$productRepository->repoProductquery($this->product($request, $productRepository)->getProduct_id()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }
    
    private function rbac(SessionInterface $session) {
        $canEdit = $this->userService->hasPermission('editProduct');
        if (!$canEdit){
            $this->flash($session,'warning', 'You do not have the required permission.');
            return $this->webService->getRedirectResponse('product/index');
        }
        return $canEdit;
    }
    
    private function product(Request $request,ProductRepository $productRepository) {
        $product_id = $request->getAttribute('product_id');       
        $product = $productRepository->repoProductquery($product_id);
        if ($product === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $product;
    }
    
    private function products(ProductRepository $productRepository) {
        $products = $productRepository->findAllPreloaded();        
        if ($products === null) {
            return $this->webService->getNotFoundResponse();
        };
        return $products;
    }
    
    private function body($product) {
        $body = [
                'product_id'=>$product->getProduct_id(),
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
    
    private function flash(SessionInterface $session, $level, $message){
        $flash = new Flash($session);
        $flash->set($level, $message); 
        return $flash;
    }
}
