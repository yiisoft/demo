<?php

declare(strict_types=1); 

namespace App\Invoice\CustomField;

use App\Invoice\Entity\CustomField;
use App\Invoice\CustomField\CustomFieldService;
use App\Invoice\CustomField\CustomFieldRepository;
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

final class CustomFieldController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService;
    private CustomFieldService $customfieldService;
    
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UserService $userService,
        CustomFieldService $customfieldService
    )    
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/customfield')
                                           ->withLayout(dirname(dirname(__DIR__)).'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->userService = $userService;
        $this->customfieldService = $customfieldService;
    }
    
    public function index(SessionInterface $session, CustomFieldRepository $customfieldRepository, SettingRepository $settingRepository, Request $request, CustomFieldService $service): Response
    {
       
         $canEdit = $this->rbac($session);
         $flash = $this->flash($session, 'dummy' , 'Flash message!.');
         $parameters = [
      
          's'=>$settingRepository,
          'canEdit' => $canEdit,
          'customfields' => $this->customfields($customfieldRepository),
          'flash'=> $flash
         ];

        if ($this->isAjaxRequest($request)) {
            return $this->viewRenderer->renderPartial('_customfields', ['data' => $paginator]);
        }
        
        return $this->viewRenderer->render('index', $parameters);
    }
    
    private function isAjaxRequest(Request $request): bool
    {
        return $request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
    }
    
    public function add(ViewRenderer $head,SessionInterface $session, Request $request, 
                        ValidatorInterface $validator,
                        SettingRepository $settingRepository                        

    )
    {
        $this->rbac($session);
        $parameters = [
            'title' => 'Add',
            'action' => ['customfield/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$settingRepository,
            'head'=>$head,
            
        ];
        
        if ($request->getMethod() === Method::POST) {
            
            $form = new CustomFieldForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->customfieldService->saveCustomField(new CustomField(),$form);
                return $this->webService->getRedirectResponse('customfield/index');
            }
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function edit(ViewRenderer $head, SessionInterface $session, Request $request, 
                        ValidatorInterface $validator,
                        CustomFieldRepository $customfieldRepository, 
                        SettingRepository $settingRepository                        

    ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => 'Edit',
            'action' => ['customfield/edit', ['id' => $this->customfield($request, $customfieldRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->customfield($request, $customfieldRepository)),
            'head'=>$head,
            's'=>$settingRepository,
            
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new CustomFieldForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->customfieldService->saveCustomField($this->customfield($request,$customfieldRepository), $form);
                return $this->webService->getRedirectResponse('customfield/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function delete(SessionInterface $session,Request $request,CustomFieldRepository $customfieldRepository 
    ): Response {
        $this->rbac($session);
        $this->flash($session, 'danger','This record has been deleted');
        $this->customfieldService->deleteCustomField($this->customfield($request,$customfieldRepository));               
        return $this->webService->getRedirectResponse('customfield/index');        
    }
    
    public function view(SessionInterface $session,Request $request,CustomFieldRepository $customfieldRepository,
        SettingRepository $settingRepository
        ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('view'),
            'action' => ['customfield/edit', ['id' => $this->customfield($request, $customfieldRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->customfield($request, $customfieldRepository)),
            's'=>$settingRepository,             
            'customfield'=>$customfieldRepository->repoCustomFieldquery($this->customfield($request, $customfieldRepository)->getId()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }
    
    private function rbac(SessionInterface $session) 
    {
        $canEdit = $this->userService->hasPermission('editCustomField');
        if (!$canEdit){
            $this->flash($session,'warning', 'You do not have the required permission.');
            return $this->webService->getRedirectResponse('customfield/index');
        }
        return $canEdit;
    }
    
    private function customfield(Request $request,CustomFieldRepository $customfieldRepository) 
    {
        $id = $request->getAttribute('id');       
        $customfield = $customfieldRepository->repoCustomFieldquery($id);
        if ($customfield === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $customfield;
    }
    
    private function customfields(CustomFieldRepository $customfieldRepository) 
    {
        $customfields = $customfieldRepository->findAllPreloaded();        
        if ($customfields === null) {
            return $this->webService->getNotFoundResponse();
        };
        return $customfields;
    }
    
    private function body($customfield) {
        $body = [
          'table'=>$customfield->getTable(),
          'label'=>$customfield->getLabel(),
          'type'=>$customfield->getType(),
          'location'=>$customfield->getLocation(),
          'order'=>$customfield->getOrder()
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