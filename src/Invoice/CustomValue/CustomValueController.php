<?php

declare(strict_types=1); 

namespace App\Invoice\CustomValue;

use App\Invoice\Entity\CustomValue;
use App\Invoice\CustomValue\CustomValueService;
use App\Invoice\CustomValue\CustomValueRepository;
use App\Invoice\Setting\SettingRepository;
use App\Invoice\CustomField\CustomFieldRepository;
use App\Service\WebControllerService;
use App\User\UserService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Http\Method;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Session\Flash\Flash;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\View\ViewRenderer;
use \Exception;

final class CustomValueController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService;
    private CustomValueService $customvalueService;
    private TranslatorInterface $translator;
        
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UserService $userService,
        CustomValueService $customvalueService,
        TranslatorInterface $translator,
    )    
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/customvalue')
                                           ->withLayout(dirname(dirname(__DIR__)).'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->userService = $userService;
        $this->customvalueService = $customvalueService;
        $this->translator = $translator;
    }
    
    public function index(SessionInterface $session, CustomValueRepository $customvalueRepository, CustomFieldRepository $customfieldRepository, SettingRepository $settingRepository, Request $request, CustomValueService $service): Response
    {
         $canEdit = $this->rbac($session);
         $flash = $this->flash($session, '','');
         $custom_field_id = $session->get('custom_field_id');
         $custom_values = $customvalueRepository->repoCustomFieldquery((int)$custom_field_id);
         $parameters = [
          'custom_field' => $customfieldRepository->repoCustomFieldquery($custom_field_id),
          'custom_field_id' => $custom_field_id,
          's'=>$settingRepository,
          'canEdit' => $canEdit,
          'custom_values' => $custom_values,
          'custom_values_types'=> array_merge($this->user_input_types(), $this->custom_value_fields()), 
          'flash'=> $flash
        ];
        return $this->viewRenderer->render('index', $parameters);
    }
     
    public function field(SessionInterface $session, CustomFieldRepository $customfieldRepository, CustomValueRepository $customvalueRepository, SettingRepository $settingRepository, CurrentRoute $currentRoute, CustomValueService $service): Response
    {      
        $canEdit = $this->rbac($session);
        $flash = $this->flash($session, '','');
        $id = $currentRoute->getArgument('id');
        null!==($session->get('custom_field_id')) ?: $session->set('custom_field_id', $id);
        $custom_field = $customfieldRepository->repoCustomFieldquery($id);
        $customvalues = $customvalueRepository->repoCustomFieldquery((int)$id);    
        if ($customvalues === null) {
            return $this->webService->getNotFoundResponse();
        }
        $parameters = [
            's'=>$settingRepository,
            'canEdit' => $canEdit,
            'custom_field' => $custom_field,
            'custom_values_types' => array_merge($this->user_input_types(), $this->custom_value_fields()), 
            'custom_values'=> $customvalues,
            'flash'=> $flash
        ];
        return $this->viewRenderer->render('field', $parameters);
    }
    
    private function isAjaxRequest(Request $request): bool
    {
        return $request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
    }
    
    public function new(ViewRenderer $head, Request $request, SessionInterface $session, CurrentRoute $currentRoute, 
                        ValidatorInterface $validator,
                        SettingRepository $settingRepository,                        
                        CustomFieldRepository $custom_fieldRepository
    )
    {
        $this->rbac($session);
        $field_id = $currentRoute->getArgument('id');        
        $session->set('custom_field_id', $field_id);
        $custom_field = $custom_fieldRepository->repoCustomFieldquery($field_id);
        $parameters = [
            'title' => 'Add',
            'action' => ['customvalue/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$settingRepository,
            'custom_field'=>$custom_field, 
            'header_buttons'=>$this->viewRenderer->renderPartialAsString('/invoice/layout/header_buttons',['hide_submit_button'=>false, 
                                                                                 'hide_cancel_button'=>false,'s'=>$settingRepository]),
            'head'=>$head,
            'custom_fields'=>$custom_fieldRepository->findAllPreloaded(),
        ];
        
        if ($request->getMethod() === Method::POST) {            
            $form = new CustomValueForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->customvalueService->saveCustomValue(new CustomValue(),$form);
                return $this->webService->getRedirectResponse('customvalue/index');
            }
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('new', $parameters);
    }
    
    public function edit(ViewRenderer $head, SessionInterface $session, Request $request, CurrentRoute $currentRoute, 
                        ValidatorInterface $validator,
                        CustomValueRepository $customvalueRepository, 
                        SettingRepository $settingRepository,                        
                        CustomFieldRepository $custom_fieldRepository
    ): Response {
        $this->rbac($session);
        $custom_field_id = $session->get('custom_field_id');
        $custom_field = $custom_fieldRepository->repoCustomFieldquery($custom_field_id);
        $parameters = [
            'title' => 'Edit',
            'action' => ['customvalue/edit', ['id' => $this->customvalue($currentRoute, $customvalueRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->customvalue($currentRoute, $customvalueRepository)),
            'header_buttons'=>$this->viewRenderer->renderPartialAsString('/invoice/layout/header_buttons',
                      ['hide_submit_button'=>false, 'hide_cancel_button'=>false,'s'=>$settingRepository]),
            'head'=>$head,
            's'=>$settingRepository,
            'custom_field' => $custom_field,
            'custom_fields'=>$custom_fieldRepository->findAllPreloaded()
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new CustomValueForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->customvalueService->saveCustomValue($this->customvalue($currentRoute,$customvalueRepository), $form);
                return $this->webService->getRedirectResponse('customvalue/index');                 
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('edit', $parameters);
    }
    
    public function delete(SessionInterface $session,CurrentRoute $currentRoute,CustomValueRepository $customvalueRepository 
    ): Response {
        $this->rbac($session);
        try {
            $this->customvalueService->deleteCustomValue($this->customvalue($currentRoute,$customvalueRepository));               
            $this->flash($session, 'info', 'Deleted.');
            return $this->webService->getRedirectResponse('customvalue/index'); 
	} catch (Exception $e) {
            //unset($e);
            $this->flash($session, 'danger', $e);
            return $this->webService->getRedirectResponse('customvalue/index'); 
        }
    }
    
    public function view(SessionInterface $session, CurrentRoute $currentRoute, Request $request,CustomValueRepository $customvalueRepository,
        SettingRepository $settingRepository,
        ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('view'),
            'action' => ['customvalue/view', ['id' => $this->customvalue($currentRoute, $customvalueRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->customvalue($currentRoute, $customvalueRepository)),
            's'=>$settingRepository,             
            'customvalue'=>$customvalueRepository->repoCustomValuequery($this->customvalue($currentRoute, $customvalueRepository)->getId()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }
        
    private function rbac(SessionInterface $session) 
    {
        $canEdit = $this->userService->hasPermission('editCustomValue');
        if (!$canEdit){
            $this->flash($session,'warning', $this->translator->translate('invoice.permission'));
            return $this->webService->getRedirectResponse('customvalue/index');
        }
        return $canEdit;
    }
    
    private function customvalue(CurrentRoute $currentRoute,CustomValueRepository $customvalueRepository) 
    {
        $id = $currentRoute->getArgument('id');       
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
        }
        return $customvalues;
    }
    
    private function body($customvalue) {
        $body = [                
          'id'=>$customvalue->getId(),             
          'custom_field_id'=>$customvalue->getCustom_field_id(),
          'value'=>$customvalue->getValue()
        ];
        return $body;
    }
    
    private function flash(SessionInterface $session, $level, $message){
        $flash = new Flash($session);
        $flash->set($level, $message); 
        return $flash;
    }
    
    /**
     * @return string[]
     */
    public function user_input_types() : array
    {
        return array(
            'TEXT',
            'DATE',
            'BOOLEAN'
        );
    }

    /**
     * @return string[]
     */
    public function custom_value_fields() : array
    {
        return array(
            'SINGLE-CHOICE',
            'MULTIPLE-CHOICE'
        );
    }
}

