<?php

declare(strict_types=1); 

namespace App\Invoice\ClientCustom;

use App\Invoice\Entity\ClientCustom;
use App\Invoice\ClientCustom\ClientCustomService;
use App\Invoice\ClientCustom\ClientCustomRepository;
use App\Invoice\Setting\SettingRepository;
use App\Invoice\Client\ClientRepository;
use App\Invoice\CustomField\CustomFieldRepository;
use App\User\UserService;
use Yiisoft\Validator\ValidatorInterface;
use App\Service\WebControllerService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Http\Method;
use Yiisoft\Yii\View\ViewRenderer;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Session\Flash\Flash;
use Yiisoft\Translator\TranslatorInterface;
use \Exception;

final class ClientCustomController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService;
    private ClientCustomService $clientcustomService;
    private TranslatorInterface $translator;
        
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UserService $userService,
        ClientCustomService $clientcustomService,
        TranslatorInterface $translator
    )    
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/clientcustom')
                                           ->withLayout(dirname(dirname(__DIR__)).'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->userService = $userService;
        $this->clientcustomService = $clientcustomService;
        $this->translator = $translator;
    }
    
    public function index(SessionInterface $session, ClientCustomRepository $clientcustomRepository, SettingRepository $settingRepository, Request $request, ClientCustomService $service): Response
    {      
        $canEdit = $this->rbac($session);
        $flash = $this->flash($session, '','');
        $parameters = [
         's'=>$settingRepository,
         'canEdit' => $canEdit,
         'clientcustoms' => $this->clientcustoms($clientcustomRepository),
         'flash'=> $flash
        ];
        
        return $this->viewRenderer->render('index', $parameters);
    } 
  
    private function isAjaxRequest(Request $request): bool
    {
        return $request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
    }
    
    public function add(ViewRenderer $head,SessionInterface $session, Request $request, 
                        ValidatorInterface $validator,
                        SettingRepository $settingRepository,                        
                        ClientRepository $clientRepository,
                        CustomFieldRepository $custom_fieldRepository
    )
    {
        $this->rbac($session);
        $parameters = [
            'title' => 'Add',
            'action' => ['clientcustom/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$settingRepository,
            'head'=>$head,
            
            'clients'=>$clientRepository->findAllPreloaded(),
            'custom_fields'=>$custom_fieldRepository->findAllPreloaded(),
        ];
        
        if ($request->getMethod() === Method::POST) {
            
            $form = new ClientCustomForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->clientcustomService->saveClientCustom(new ClientCustom(),$form);
                return $this->webService->getRedirectResponse('clientcustom/index');
            }
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function edit(ViewRenderer $head, SessionInterface $session, CurrentRoute $currentRoute, Request $request,
                        ValidatorInterface $validator,
                        ClientCustomRepository $clientcustomRepository, 
                        SettingRepository $settingRepository,                        
                        ClientRepository $clientRepository,
                        CustomFieldRepository $custom_fieldRepository
    ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => 'Edit',
            'action' => ['clientcustom/edit', ['id' => $this->clientcustom($currentRoute, $clientcustomRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->clientcustom($currentRoute, $clientcustomRepository)),
            'head'=>$head,
            's'=>$settingRepository,
                        'clients'=>$clientRepository->findAllPreloaded(),
            'custom_fields'=>$custom_fieldRepository->findAllPreloaded()
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new ClientCustomForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->clientcustomService->saveClientCustom($this->clientcustom($currentRoute, $clientcustomRepository), $form);
                return $this->webService->getRedirectResponse('clientcustom/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function delete(SessionInterface $session, CurrentRoute $currentRoute, ClientCustomRepository $clientcustomRepository 
    ): Response {
        $this->rbac($session);
        try {
            $this->clientcustomService->deleteClientCustom($this->clientcustom($currentRoute,$clientcustomRepository));               
            $this->flash($session, 'info', 'Deleted.');
            return $this->webService->getRedirectResponse('clientcustom/index'); 
	} catch (Exception $e) {
            //unset($e);
            $this->flash($session, 'danger', $e);
            return $this->webService->getRedirectResponse('clientcustom/index'); 
        }
    }
    
    public function view(SessionInterface $session, CurrentRoute $currentRoute, ClientCustomRepository $clientcustomRepository,
        SettingRepository $settingRepository,
        ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('view'),
            'action' => ['clientcustom/view', ['id' => $this->clientcustom($currentRoute, $clientcustomRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->clientcustom($currentRoute, $clientcustomRepository)),
            's'=>$settingRepository,             
            'clientcustom'=>$clientcustomRepository->repoClientCustomquery($this->clientcustom($currentRoute, $clientcustomRepository)->getId()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }
        
    private function rbac(SessionInterface $session) 
    {
        $canEdit = $this->userService->hasPermission('editClientCustom');
        if (!$canEdit){
            $this->flash($session,'warning', $this->translator->translate('invoice.permission'));
            return $this->webService->getRedirectResponse('clientcustom/index');
        }
        return $canEdit;
    }
    
    private function clientcustom(CurrentRoute $currentRoute,ClientCustomRepository $clientcustomRepository) 
    {
        $id = $currentRoute->getArgument('id');       
        $clientcustom = $clientcustomRepository->repoClientCustomquery($id);
        if ($clientcustom === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $clientcustom;
    }
    
    private function clientcustoms(ClientCustomRepository $clientcustomRepository) 
    {
        $clientcustoms = $clientcustomRepository->findAllPreloaded();        
        if ($clientcustoms === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $clientcustoms;
    }
    
    private function body($clientcustom) {
        $body = [
                
          'id'=>$clientcustom->getId(),
          'client_id'=>$clientcustom->getClient_id(),
          'custom_field_id'=>$clientcustom->getCustom_field_id(),
          'value'=>$clientcustom->getValue()
                ];
        return $body;
    }
    
    private function flash(SessionInterface $session, $level, $message){
        $flash = new Flash($session);
        $flash->set($level, $message); 
        return $flash;
    }
}

