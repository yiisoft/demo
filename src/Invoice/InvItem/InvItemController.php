<?php

declare(strict_types=1); 

namespace App\Invoice\InvItem;

use App\Invoice\Entity\InvItem;
use App\Invoice\Product\ProductRepository as PR; 
use App\Invoice\Inv\InvRepository as IR;
use App\Invoice\InvItem\InvItemService;
use App\Invoice\InvItem\InvItemForm;
use App\Invoice\InvItem\InvItemRepository as IIR;
use App\Invoice\InvItemAmount\InvItemAmountRepository as IIAR;
use App\Invoice\InvItemAmount\InvItemAmountService as IIAS;
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

final class InvItemController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService;
    private InvItemService $invitemService;    
    private DataResponseFactoryInterface $factory;
    private UrlGenerator $urlGenerator;
    private TranslatorInterface $translator;
    
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UserService $userService,
        InvItemService $invitemService,        
        DataResponseFactoryInterface $factory,
        UrlGenerator $urlGenerator,
        TranslatorInterface $translator,
    )    
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/invitem')
                                           ->withLayout(dirname(dirname(__DIR__)) .'/Invoice/Layout/main.php');                                                
        $this->webService = $webService;
        $this->userService = $userService;
        $this->invitemService = $invitemService;
        $this->factory = $factory;
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
    }
    
    public function add(ViewRenderer $head, SessionInterface $session, Request $request, 
                        ValidatorInterface $validator,
                        SR $sR,
                        PR $pR,
                        UR $uR,                                                
                        TRR $trR,
                        IIAR $iiar,
    ) : Response
    {
        $inv_id = $session->get('inv_id');
        $this->rbac($session);
        $parameters = [
            'title' => 'Add',
            'action' => ['invitem/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$sR,
            'head'=>$head,
            'inv_id'=>$inv_id,
            'tax_rates'=>$trR->findAllPreloaded(),
            'products'=>$pR->findAllPreloaded(),
            'units'=>$uR->findAllPreloaded(),
            'numberhelper'=>new NumberHelper($sR)
        ];
        
        if ($request->getMethod() === Method::POST) {            
            $form = new InvItemForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                try {  
                  $this->invitemService->saveInvItem(new InvItem(), $form, $inv_id, $pR, $trR, new IIAS($iiar), $iiar, $uR);
                  $this->flash($session, 'info', $sR->trans('record_successfully_added'));
                  return $this->factory->createResponse($this->viewRenderer->renderPartialAsString('/invoice/setting/inv_successful',
                         ['heading'=>'Successful','_language'=>'en',
                          'message'=>$sR->trans('record_successfully_added'),'url'=>'inv/view','id'=>$inv_id]));  
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
                                                      ['heading'=>'Not successful. '.$message,'_language'=>'en',
                                                       'message'=>$sR->trans('record_successfully_added'),'url'=>'inv/view','id'=>$inv_id]));  
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
    
    private function body($invitem) {
        $body = [
          'id'=>$invitem->getId(),
          'inv_id'=>$invitem->getInv_id(),
          'tax_rate_id'=>$invitem->getTax_rate_id(),
          'product_id'=>$invitem->getProduct_id(),
          'name'=>$invitem->getName(),
          'description'=>$invitem->getDescription(),
          'quantity'=>$invitem->getQuantity(),
          'price'=>$invitem->getPrice(),
          'discount_amount'=>$invitem->getDiscount_amount(),
          'order'=>$invitem->getOrder(),
          'product_unit'=>$invitem->getProduct_unit(),
          'product_unit_id'=>$invitem->getProduct_unit_id()
        ];
        return $body;
    }
       
    public function edit(ViewRenderer $head, SessionInterface $session, CurrentRoute $currentRoute, Request $request, ValidatorInterface $validator,
                        IIR $iiR, SR $sR, TRR $trR, PR $pR, UR $uR, IR $iR, IIAS $iias, IIAR $iiar): Response {
        $this->rbac($session);
        $inv_id = $session->get('inv_id');
        $parameters = [
            'title' => 'Edit',
            'action' => ['invitem/edit', ['id' => $this->invitem($currentRoute, $iiR)->getId()]],
            'errors' => [],
            'body' => $this->body($this->invitem($currentRoute, $iiR)),
            'inv_id'=>$inv_id,
            'head'=>$head,
            's'=>$sR,
            'tax_rates'=>$trR->findAllPreloaded(),
            'products'=>$pR->findAllPreloaded(),
            'invs'=>$iR->findAllPreloaded(),            
            'units'=>$uR->findAllPreloaded(),
            'numberhelper'=>new NumberHelper($sR)
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new InvItemForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
            try {    
                $this->invitemService->saveInvItem($this->invitem($currentRoute, $iiR), $form, $inv_id, $pR, $trR , $iias, $iiar, $uR);
                 return $this->factory->createResponse($this->viewRenderer->renderPartialAsString('/invoice/setting/inv_successful',
                 ['heading'=>'Successful','message'=>$sR->trans('record_successfully_added'),'url'=>'inv/view','id'=>$inv_id])); 
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
                 ['heading'=>'Not successful','message'=>$message,'url'=>'inv/view','id'=>$inv_id])); 
                }
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFormErrors();
        } 
        return $this->viewRenderer->render('_item_edit_form', $parameters);
    }
    
    public function delete(CurrentRoute $currentRoute, IIR $iiR): Response {
            $iiR->repoInvItemCount($this->invitem($currentRoute, $iiR)->getId()) === 1  ? (($this->invitemService->deleteInvItem($this->invitem($currentRoute, $iiR)))): '';
            return $this->viewRenderer->render('inv/index');
    }            
   
    private function flash(SessionInterface $session, $level, $message){
        $flash = new Flash($session);
        $flash->set($level, $message); 
        return $flash;
    }
    
    public function index(SessionInterface $session, IIR $iiR, SR $sR): Response
    {       
         $canEdit = $this->rbac($session);
         $flash = $this->flash($session, '','');
         $parameters = [      
          's'=>$sR,
          'inv_id'=>$session->get('inv_id'),
          'canEdit' => $canEdit,
          'invitems' => $this->invitems($iiR),
          'flash'=> $flash
         ];
        
        return $this->viewRenderer->render('index', $parameters);
    } 
    
    private function invitem(CurrentRoute $currentRoute, IIR $iiR) 
    {
        $id = $currentRoute->getArgument('id'); 
        $invitem = ($iiR->repoInvItemCount($id) === 1 ? $iiR->repoInvItemquery($id) : '');
        return $invitem;
    }
    
    private function invitems(IIR $iiR) 
    {
        $invitems = $iiR->findAllPreloaded();        
        if ($invitems === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $invitems;
    }
    
    
    private function isAjaxRequest(Request $request): bool
    {
        return $request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
    }
    
    public function multiple(Request $request, IIR $iiR): Response {
        //jQuery parameters from inv.js function delete-items-confirm-inv 'item_ids' and 'inv_id'
        $select_items = $request->getQueryParams() ?? [];
        $result = false;
        $item_ids = ($select_items['item_ids'] ? $select_items['item_ids'] : []);
        $items = $iiR->findinInvItems($item_ids);
        // If one item is deleted, the result is positive
        foreach ($items as $item){
            ($this->invitemService->deleteInvItem($item));
            $result = true;
        }
        return $this->factory->createResponse(Json::encode(($result ? ['success'=>1]:['success'=>0])));  
    }
    
    private function rbac(SessionInterface $session) 
    {
        $canEdit = $this->userService->hasPermission('editInvItem');
        if (!$canEdit){
            $this->flash($session,'warning', $this->translator->translate('invoice.permission'));
            return $this->webService->getRedirectResponse('invitem/index');
        }
        return $canEdit;
    }
    
    public function view(SessionInterface $session, CurrentRoute $currentRoute, IIR $iiR,
        SR $sR 
        ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $sR->trans('view'),
            'action' => ['invitem/edit', ['id' => $this->invitem($currentRoute, $iiR)->getId()]],
            'errors' => [],
            'body' => $this->body($this->invitem($currentRoute, $iiR)),
            's'=>$sR,             
            'invitem'=>$iiR->repoInvItemquery($this->invitem($currentRoute, $iiR)->getId()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    } 
}