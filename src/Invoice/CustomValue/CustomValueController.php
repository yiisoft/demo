<?php

declare(strict_types=1); 

namespace App\Invoice\CustomValue;

use App\Invoice\Entity\CustomValue;
use App\Invoice\CustomValue\CustomValueService;
use App\Invoice\CustomValue\CustomValueRepository;
use App\Invoice\Setting\SettingRepository;
use App\User\UserService;
use Yiisoft\Validator\ValidatorInterface;
use App\Service\WebControllerService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Http\Method;
use Yiisoft\Yii\View\ViewRenderer;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Session\Flash\Flash;

final class CustomValueController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService;
    private CustomValueService $customvalueService;
    
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UserService $userService,
        CustomValueService $customvalueService
    )    
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/customvalue')
                                           ->withLayout(dirname(dirname(__DIR__)).'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->userService = $userService;
        $this->customvalueService = $customvalueService;
    }
    
    public function index(SessionInterface $session, CustomValueRepository $customvalueRepository, SettingRepository $settingRepository, Request $request, CustomValueService $service): Response
    {
       
         $canEdit = $this->rbac($session);
         $flash = $this->flash($session, 'dummy' , 'Flash message!.');
         $parameters = [
      
          's'=>$settingRepository,
          'canEdit' => $canEdit,
          'customvalues' => $this->customvalues($customvalueRepository),
          'flash'=> $flash
         ];

        if ($this->isAjaxRequest($request)) {
            return $this->viewRenderer->renderPartial('_customvalues', ['data' => $paginator]);
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
            'action' => ['customvalue/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$settingRepository,
            'head'=>$head,
            
        ];
        
        if ($request->getMethod() === Method::POST) {
            
            $form = new CustomValueForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->customvalueService->saveCustomValue(new CustomValue(),$form);
                return $this->webService->getRedirectResponse('customvalue/index');
            }
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function edit(ViewRenderer $head, SessionInterface $session, Request $request, 
                        ValidatorInterface $validator,
                        CustomValueRepository $customvalueRepository, 
                        SettingRepository $settingRepository,                        

    ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => 'Edit',
            'action' => ['customvalue/edit', ['id' => $this->customvalue($request, $customvalueRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->customvalue($request, $customvalueRepository)),
            'head'=>$head,
            's'=>$settingRepository,
            
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new CustomValueForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->customvalueService->saveCustomValue($this->customvalue($request,$customvalueRepository), $form);
                return $this->webService->getRedirectResponse('customvalue/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function delete(SessionInterface $session,Request $request,CustomValueRepository $customvalueRepository 
    ): Response {
        $this->rbac($session);
       
        $this->customvalueService->deleteCustomValue($this->customvalue($request,$customvalueRepository));               
        return $this->webService->getRedirectResponse('customvalue/index');        
    }
    
    public function view(SessionInterface $session,Request $request,CustomValueRepository $customvalueRepository,
        SettingRepository $settingRepository,
        ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('view'),
            'action' => ['customvalue/edit', ['id' => $this->customvalue($request, $customvalueRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->customvalue($request, $customvalueRepository)),
            's'=>$settingRepository,             
            'customvalue'=>$customvalueRepository->repoCustomValuequery($this->customvalue($request, $customvalueRepository)->getId()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }
    
    private function rbac(SessionInterface $session) 
    {
        $canEdit = $this->userService->hasPermission('editCustomValue');
        if (!$canEdit){
            $this->flash($session,'warning', 'You do not have the required permission.');
            return $this->webService->getRedirectResponse('customvalue/index');
        }
        return $canEdit;
    }
    
    private function customvalue(Request $request,CustomValueRepository $customvalueRepository) 
    {
        $id = $request->getAttribute('id');       
        $customvalue = $customvalueRepository->repoCustomValuequery($id);
        if ($customvalue === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $customvalue;
    }
    
    private function customvalues(CustomValueRepository $customvalueRepository) 
    {
        $customvalues = $customvalueRepository->findAllPreloaded();        
        if ($customvalues === null) {
            return $this->webService->getNotFoundResponse();
        };
        return $customvalues;
    }
    
    private function body($customvalue) {
        $body = [
                
          'id'=>$customvalue->getId(),
          'field'=>$customvalue->getField(),
          'value'=>$customvalue->getValue()
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