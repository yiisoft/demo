<?php

declare(strict_types=1); 

namespace App\Invoice\CustomField;

use App\Invoice\Entity\CustomField;
use App\Invoice\CustomField\CustomFieldService;
use App\Invoice\CustomField\CustomFieldRepository;
use App\Invoice\Setting\SettingRepository;
use App\User\UserService;
use App\Service\WebControllerService;
// Psr
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
// Yii
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Http\Method;
use Yiisoft\Json\Json;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Session\Flash\Flash;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\View\ViewRenderer;

final class CustomFieldController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService;
    private CustomFieldService $customfieldService;   
    private TranslatorInterface $translator;
    
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UserService $userService,
        CustomFieldService $customfieldService,
        TranslatorInterface $translator,
    )    
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/customfield')
                                           ->withLayout(dirname(dirname(__DIR__)).'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->userService = $userService;
        $this->customfieldService = $customfieldService;
        $this->translator = $translator;
    }
    
    public function index(SessionInterface $session, CustomFieldRepository $customfieldRepository, SettingRepository $settingRepository, Request $request): Response
    {
        $pageNum = (int)$request->getAttribute('page', '1');
        $paginator = (new OffsetPaginator($this->customfields($customfieldRepository)))
        ->withPageSize((int)$settingRepository->setting('default_list_limit'))
        ->withCurrentPage($pageNum);
      
        $canEdit = $this->rbac($session);
        $flash = $this->flash($session, 'info' , $this->viewRenderer->renderPartialAsString('/invoice/info/custom_field'));
        $parameters = [
              'paginator' => $paginator,  
              's'=>$settingRepository,
              'canEdit' => $canEdit,
              'max'=>$settingRepository->setting('default_list_limit'),
              'customfields' => $this->customfields($customfieldRepository),
              'custom_tables' => $this->custom_tables(),            
              'custom_value_fields'=>$this->custom_value_fields(),
              'flash'=> $flash,
       ];    
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
            'tables' => $this->custom_tables(),
            'user_input_types'=>['NUMBER','TEXT','DATE','BOOLEAN'],
            'custom_value_fields'=>['SINGLE-CHOICE','MULTIPLE-CHOICE'],
            'layout_header_buttons'=>$this->viewRenderer->renderPartialAsString('/invoice/layout/header_buttons',
            [
                     'hide_submit_button'=>false,
                     'hide_cancel_button'=>false,
                     's'=>$settingRepository   
            ]),
            // Create an array for "moduled" ES6 jquery script. The script is "moduled" and therefore deferred by default to avoid
            // the $ undefined reference error in the DOM.
            'positions'=>$this->positions($settingRepository)
        ];
        
        if ($request->getMethod() === Method::POST) {            
            $form = new CustomFieldForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->customfieldService->saveCustomField(new CustomField(),$form);
                return $this->webService->getRedirectResponse('customfield/index');
            }
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function edit(ViewRenderer $head, SessionInterface $session, Request $request, CurrentRoute $currentRoute,
                        ValidatorInterface $validator,
                        CustomFieldRepository $customfieldRepository, 
                        SettingRepository $settingRepository                        

    ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => 'Edit',
            'action' => ['customfield/edit', ['id' => $this->customfield($currentRoute, $customfieldRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->customfield($currentRoute, $customfieldRepository)),
            'head'=>$head,
            's'=>$settingRepository,
            'head'=>$head,
            'tables' => $this->custom_tables(),
            'user_input_types'=>['NUMBER','TEXT','DATE','BOOLEAN'],
            'custom_value_fields'=>['SINGLE-CHOICE','MULTIPLE-CHOICE'],
            'layout_header_buttons'=>$this->viewRenderer->renderPartialAsString('/invoice/layout/header_buttons',
            [
                     'hide_submit_button'=>false,
                     'hide_cancel_button'=>false,
                     's'=>$settingRepository   
            ]),
            'positions'=>$this->positions($settingRepository)    
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new CustomFieldForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->customfieldService->saveCustomField($this->customfield($currentRoute,$customfieldRepository), $form);
                return $this->webService->getRedirectResponse('customfield/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function delete(SessionInterface $session,CurrentRoute $currentRoute, CustomFieldRepository $customfieldRepository 
    ): Response {
        $this->rbac($session);       
        $this->customfieldService->deleteCustomField($this->customfield($currentRoute, $customfieldRepository));               
        return $this->webService->getRedirectResponse('customfield/index');        
    }
    
    public function view(SessionInterface $session, CurrentRoute $currentRoute,CustomFieldRepository $customfieldRepository,
        SettingRepository $settingRepository
        ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('view'),
            'action' => ['customfield/edit', ['id' => $this->customfield($currentRoute, $customfieldRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->customfield($currentRoute, $customfieldRepository)),
            's'=>$settingRepository,             
            'customfield'=>$customfieldRepository->repoCustomFieldquery($this->customfield($currentRoute, $customfieldRepository)->getId()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }
    
    private function rbac(SessionInterface $session) 
    {
        $canEdit = $this->userService->hasPermission('editCustomField');
        if (!$canEdit){
            $this->flash($session,'warning', $this->translator->translate('invoice.permission'));
            return $this->webService->getRedirectResponse('customfield/index');
        }
        return $canEdit;
    }
    
    private function customfield(CurrentRoute $currentRoute, CustomFieldRepository $customfieldRepository) 
    {
        $id = $currentRoute->getArgument('id');       
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
        }
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
    
    private function positions(SettingRepository $s) : string {
        // The default position on the form is custom fields so if none of the other options are chosen then the new field
        // will appear under the default custom field section. The client form has five areas where the new field can appear.
        $positions = [
                    'client' =>  ['custom_fields','address','contact_information','personal_information','tax_information'],
                    // A custom field created with "properties" will appear in the address section 
                    'invoice' => ['custom_fields','properties'],                    
                    'payment' => ['custom_fields'],
                    'quote' =>   ['custom_fields','properties'],
                    'user' =>    ['custom_fields','account_information','address','tax_information','contact_information'],
                ];
                foreach ($positions as $key => $val) {
                    foreach ($val as $key2 => $val2) {
                        $val[$key2] = $s->trans($val2);
                    }
                    $positions[$key] = $val;
                }
        return Json::encode($positions);
    }
    
    /**
     * @return array
     */
    private function custom_tables()
    {
        return [
            'client_custom' => 'client',
            'inv_custom' => 'invoice',
            'payment_custom' => 'payment',
            'quote_custom' => 'quote',
            'user_custom' => 'user',
        ];
    }
    
    /**
     * @return string[]
     */
    public static function custom_value_fields()
    {
        return array(
            'SINGLE-CHOICE',
            'MULTIPLE-CHOICE'
        );
    }
    
}