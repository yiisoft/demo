<?php

declare(strict_types=1); 

namespace App\Invoice\ItemLookup;

use App\Invoice\Entity\ItemLookup;
use App\Invoice\ItemLookup\ItemLookupService;
use App\Invoice\ItemLookup\ItemLookupRepository;
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

final class ItemLookupController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService;
    private ItemLookupService $itemlookupService;
    private TranslatorInterface $translator;
    
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UserService $userService,
        ItemLookupService $itemlookupService,
        TranslatorInterface $translator
    )    
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/itemlookup')
                                           ->withLayout(dirname(dirname(__DIR__)) .'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->userService = $userService;
        $this->itemlookupService = $itemlookupService;
        $this->translator = $translator;
    }
    
    public function index(SessionInterface $session, ItemLookupRepository $itemlookupRepository, SettingRepository $settingRepository, Request $request, ItemLookupService $service): Response
    {
       
         $canEdit = $this->rbac($session);
         $flash = $this->flash($session, '','');
         $parameters = [
      
          's'=>$settingRepository,
          'canEdit' => $canEdit,
          'itemlookups' => $this->itemlookups($itemlookupRepository),
          'flash'=> $flash
         ];

        if ($this->isAjaxRequest($request)) {
            return $this->viewRenderer->renderPartial('_itemlookups', ['data' => $paginator]);
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

    )
    {
        $this->rbac($session);
        $parameters = [
            'title' => 'Add',
            'action' => ['itemlookup/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$settingRepository,
            'head'=>$head,
            
        ];
        
        if ($request->getMethod() === Method::POST) {
            
            $form = new ItemLookupForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->itemlookupService->saveItemLookup(new ItemLookup(),$form);
                return $this->webService->getRedirectResponse('itemlookup/index');
            }
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function edit(ViewRenderer $head, SessionInterface $session, Request $request, CurrentRoute $currentRoute,
                        ValidatorInterface $validator,
                        ItemLookupRepository $itemlookupRepository, 
                        SettingRepository $settingRepository,                        

    ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => 'Edit',
            'action' => ['itemlookup/edit', ['id' => $this->itemlookup($currentRoute, $itemlookupRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->itemlookup($currentRoute, $itemlookupRepository)),
            'head'=>$head,
            's'=>$settingRepository,            
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new ItemLookupForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->itemlookupService->saveItemLookup($this->itemlookup($currentRoute, $itemlookupRepository), $form);
                return $this->webService->getRedirectResponse('itemlookup/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function delete(SessionInterface $session, CurrentRoute $currentRoute, ItemLookupRepository $itemlookupRepository 
    ): Response {
        $this->rbac($session);       
        $this->itemlookupService->deleteItemLookup($this->itemlookup($currentRoute, $itemlookupRepository));               
        return $this->webService->getRedirectResponse('itemlookup/index');        
    }
    
    public function view(SessionInterface $session, CurentRoute $currentRoute, ItemLookupRepository $itemlookupRepository,
        SettingRepository $settingRepository,
        ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('view'),
            'action' => ['itemlookup/edit', ['id' => $this->itemlookup($currentRoute, $itemlookupRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->itemlookup($currentRoute, $itemlookupRepository)),
            's'=>$settingRepository,             
            'itemlookup'=>$itemlookupRepository->repoItemLookupquery($this->itemlookup($currentRoute, $itemlookupRepository)->getId()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }
    
    private function rbac(SessionInterface $session) 
    {
        $canEdit = $this->userService->hasPermission('editItemLookup');
        if (!$canEdit){
            $this->flash($session,'warning', $this->translator->translate('invoice.permission'));
            return $this->webService->getRedirectResponse('itemlookup/index');
        }
        return $canEdit;
    }
    
    private function itemlookup(CurrentRoute $currentRoute, ItemLookupRepository $itemlookupRepository) 
    {
        $id = $currentRoute->getArgument('id');       
        $itemlookup = $itemlookupRepository->repoItemLookupquery($id);
        if ($itemlookup === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $itemlookup;
    }
    
    private function itemlookups(ItemLookupRepository $itemlookupRepository) 
    {
        $itemlookups = $itemlookupRepository->findAllPreloaded();        
        if ($itemlookups === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $itemlookups;
    }
    
    private function body($itemlookup) {
        $body = [
                
          'id'=>$itemlookup->getId(),
          'name'=>$itemlookup->getName(),
          'description'=>$itemlookup->getDescription(),
          'price'=>$itemlookup->getPrice()
                ];
        return $body;
    }
    
    private function flash(SessionInterface $session, $level, $message){
        $flash = new Flash($session);
        $flash->set($level, $message); 
        return $flash;
    }
}