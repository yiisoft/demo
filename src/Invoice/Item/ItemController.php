<?php

declare(strict_types=1); 

namespace App\Invoice\Item;

use App\Invoice\Entity\Item;
use App\Invoice\Item\ItemService;
use App\Invoice\Item\ItemRepository;
use App\Invoice\Setting\SettingRepository;
use App\Invoice\Inv\InvRepository;
use App\Invoice\TaxRate\TaxRateRepository;
use App\Invoice\Product\ProductRepository;
use App\Invoice\Unit\UnitRepository;
use App\Invoice\Task\TaskRepository;
use App\User\UserService;
use Yiisoft\Validator\ValidatorInterface;
use App\Service\WebControllerService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Http\Method;
use Yiisoft\Yii\View\ViewRenderer;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Session\Flash\Flash;

final class ItemController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService;
    private ItemService $itemService;
    
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UserService $userService,
        ItemService $itemService
    )    
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/item')
                                           ->withLayout(dirname(dirname(__DIR__)).'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->userService = $userService;
        $this->itemService = $itemService;
    }
    
    public function index(SessionInterface $session, ItemRepository $itemRepository, SettingRepository $settingRepository, Request $request, ItemService $service): Response
    {
       
         $canEdit = $this->rbac($session);
         $flash = $this->flash($session, 'success' , 'Change the type from success to info and you will get a flash message!.');
         $parameters = [
      
          's'=>$settingRepository,
          'canEdit' => $canEdit,
          'items' => $this->items($itemRepository),
          'flash'=> $flash
         ];

        if ($this->isAjaxRequest($request)) {
            return $this->viewRenderer->renderPartial('_items', ['data' => $paginator]);
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
                        InvRepository $invRepository,
                        TaxRateRepository $tax_rateRepository,
                        ProductRepository $productRepository,
                        UnitRepository $unitRepository,
                        TaskRepository $taskRepository
    )
    {
        $this->rbac($session);
        $parameters = [
            'title' => 'Add',
            'action' => ['item/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$settingRepository,
            'head'=>$head,
            'invs'=>$invRepository->findAllPreloaded(),
            'tax_rates'=>$tax_rateRepository->findAllPreloaded(),
            'products'=>$productRepository->findAllPreloaded(),
            'units'=>$unitRepository->findAllPreloaded(),
            'tasks'=>$taskRepository->findAllPreloaded(),
        ];
        
        if ($request->getMethod() === Method::POST) {
            
            $form = new ItemForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->itemService->saveItem(new Item(),$form);
                return $this->webService->getRedirectResponse('item/index');
            }
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function edit(ViewRenderer $head, SessionInterface $session, Request $request, 
                        ValidatorInterface $validator,
                        ItemRepository $itemRepository, 
                        SettingRepository $settingRepository,                        
                        InvRepository $invRepository,
                        TaxRateRepository $tax_rateRepository,
                        ProductRepository $productRepository,
                        UnitRepository $unitRepository,
                        TaskRepository $taskRepository
    ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => 'Edit',
            'action' => ['item/edit', ['id' => $this->item($request, $itemRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->item($request, $itemRepository)),
            'head'=>$head,
            's'=>$settingRepository,
            'head'=>$head,
            'invs'=>$invRepository->findAllPreloaded(),
            'tax_rates'=>$tax_rateRepository->findAllPreloaded(),
            'products'=>$productRepository->findAllPreloaded(),
            'units'=>$unitRepository->findAllPreloaded(),
            'tasks'=>$taskRepository->findAllPreloaded()
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new ItemForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->itemService->saveItem($this->item($request,$itemRepository), $form);
                return $this->webService->getRedirectResponse('item/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function delete(SessionInterface $session,Request $request,ItemRepository $itemRepository 
    ): Response {
        $this->rbac($session);
       
        $this->itemService->deleteItem($this->item($request,$itemRepository));               
        return $this->webService->getRedirectResponse('item/index');        
    }
    
    public function view(SessionInterface $session,Request $request,ItemRepository $itemRepository,
        SettingRepository $settingRepository,
        ValidatorInterface $validator
        ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('view'),
            'action' => ['item/edit', ['id' => $this->item($request, $itemRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->item($request, $itemRepository)),
            's'=>$settingRepository,
            //load Entity\Product BelongTo relations ie. $family, $tax_rate, $unit by means of repoProductQuery             
            'item'=>$itemRepository->repoItemquery($this->item($request, $itemRepository)->getId()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }
    
    private function rbac(SessionInterface $session) 
    {
        $canEdit = $this->userService->hasPermission('editItem');
        if (!$canEdit){
            $this->flash($session,'warning', 'You do not have the required permission.');
            return $this->webService->getRedirectResponse('item/index');
        }
        return $canEdit;
    }
    
    private function item(Request $request,ItemRepository $itemRepository) 
    {
        $id = $request->getAttribute('id');       
        $item = $itemRepository->repoItemquery($id);
        if ($item === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $item;
    }
    
    private function items(ItemRepository $itemRepository) 
    {
        $items = $itemRepository->findAllPreloaded();        
        if ($items === null) {
            return $this->webService->getNotFoundResponse();
        };
        return $items;
    }
    
    private function body($item) {
        $body = [
                
          'id'=>$item->getId(),
          'inv_id'=>$item->getInv_id(),
          'tax_rate_id'=>$item->getTax_rate_id(),
          'product_id'=>$item->getProduct_id(),
          'date_added'=>$item->getDate_added(),
          'task_id'=>$item->getTask_id(),
          'name'=>$item->getName(),
          'description'=>$item->getDescription(),
          'quantity'=>$item->getQuantity(),
          'price'=>$item->getPrice(),
          'discount_amount'=>$item->getDiscount_amount(),
          'order'=>$item->getOrder(),
          'is_recurring'=>$item->getIs_recurring(),
          'unit'=>$item->getUnit(),
          'unit_id'=>$item->getUnit_id(),
          'date'=>$item->getDate()
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