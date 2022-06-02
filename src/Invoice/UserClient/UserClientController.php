<?php
declare(strict_types=1); 

namespace App\Invoice\UserClient;

use App\Invoice\Entity\UserClient;
use App\Invoice\Client\ClientRepository;
use App\Invoice\Setting\SettingRepository;
use App\Invoice\UserClient\UserClientService;
use App\Invoice\UserClient\UserClientRepository;
use App\Invoice\UserClient\UserClientForm;
use App\Invoice\UserInv\UserInvRepository as UIR;
use App\User\UserService;
use App\Service\WebControllerService;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Yiisoft\DataResponse\DataResponseFactoryInterface;
use Yiisoft\Http\Method;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Session\Flash\Flash;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\View\ViewRenderer;

use \Exception;

final class UserClientController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService;
    private UserClientService $userclientService;
    private DataResponseFactoryInterface $factory;
    private TranslatorInterface $translator;
        
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UserService $userService,
        UserClientService $userclientService,
        DataResponseFactoryInterface $factory,
        TranslatorInterface $translator,
    )    
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/userclient')
                                           ->withLayout(dirname(dirname(__DIR__)).'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->userService = $userService;
        $this->userclientService = $userclientService;
        $this->factory = $factory;
        $this->translator = $translator;
    }
    
    public function index(SessionInterface $session, UserClientRepository $userclientRepository, SettingRepository $settingRepository, Request $request, UserClientService $service): Response
    {      
        $canEdit = $this->rbac($session);
        $flash = $this->flash($session, '' , '');
        $parameters = [
          's'=>$settingRepository,
          'canEdit' => $canEdit,
          'userclients' => $this->userclients($userclientRepository),
          'flash'=> $flash,
        ];
        return $this->viewRenderer->render('index', $parameters);
    }
    
    public function add(ViewRenderer $head, SessionInterface $session, Request $request, 
                        ValidatorInterface $validator,
                        SettingRepository $settingRepository,                        

    ) : Response
    {
        $this->rbac($session);
        $parameters = [
            'title' => 'Add',
            'action' => ['userclient/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$settingRepository,
            'head'=>$head,            
        ];
        
        if ($request->getMethod() === Method::POST) {
            
            $form = new UserClientForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->userclientService->saveUserClient(new UserClient(),$form);
                return $this->webService->getRedirectResponse('userclient/index');
            }
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function delete(SessionInterface $session, CurrentRoute $currentRoute,
                           SettingRepository $sR, UserClientRepository $userclientRepository, UIR $uiR
    ): Response {
        $this->rbac($session);
        try {
            $user_id = ($this->userclient($currentRoute,$userclientRepository))->getUser_Id();
            $this->userclientService->deleteUserClient($this->userclient($currentRoute,$userclientRepository));               
            //$this->flash($session, 'info', $sR->trans('record_successfully_deleted'));
            $user_inv = $uiR->repoUserInvUserIdquery((string)$user_id);
            return $this->factory->createResponse($this->viewRenderer->renderPartialAsString('/invoice/setting/userclient_successful',
            ['heading'=>$sR->trans('client'),'message'=>$sR->trans('record_successfully_deleted'),'url'=>'userinv/client','id'=>$user_inv->getId()]));  
	} catch (Exception $e) {
            //unset($e);
            $this->flash($session, 'danger', $e);
            return $this->webService->getRedirectResponse('userclient/index'); 
        }
    }
    
    public function edit(ViewRenderer $head, SessionInterface $session, Request $request, CurrentRoute $currentRoute, 
                        ValidatorInterface $validator,
                        UserClientRepository $userclientRepository, 
                        SettingRepository $settingRepository,                        

    ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => 'Edit',
            'action' => ['userclient/edit', ['id' => $this->userclient($currentRoute, $userclientRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->userclient($currentRoute, $userclientRepository)),
            'head'=>$head,
            's'=>$settingRepository,
            
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new UserClientForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->userclientService->saveUserClient($this->userclient($currentRoute,$userclientRepository), $form);
                return $this->webService->getRedirectResponse('userclient/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    // The preceding url is userinv/client/{userinv_id} showing the currently assigned clients to this user
    
    // Retrieves userclient/new.php which offers an 'all client option' and an individual client option
    public function new(SessionInterface $session, Request $request, ValidatorInterface $validator, ViewRenderer $head, CurrentRoute $currentRoute, 
                        ClientRepository $cR, SettingRepository $sR, UserClientRepository $ucR, UserClientService $ucS, UIR $uiR) {
        
        $user_id = $currentRoute->getArgument('user_id');
        $available_client_id_list = $ucR->get_not_assigned_to_user($user_id, $cR);
        $parameters = [
            'head'=>$head,
            's'=>$sR,
            'userinv'=>$this->user($currentRoute,$uiR),
            // Only provide clients NOT already included ie. available
            'clients'=>$cR->repoUserClient($available_client_id_list),
            'flash'=>$this->flash($session,'',''),
            // Initialize the checkbox to zero so that both 'all_clients' and dropdownbox is presented on userclient/new.php
            'user_all_clients'=>'0',            
            'body'=>$request->getParsedBody()
        ];
        
        if ($request->getMethod() === Method::POST) {
            $body = $request->getParsedBody();
            foreach ($body as $key => $value) {
                // If the user is allowed to see all clients eg. An Accountant
                if (((string)$key === 'user_all_clients') && ((string)$value === '1')) {
                    // Unassign currently assigned clients
                    $ucR->unassign_to_user_client($user_id);
                    // Search for all clients, including new clients and assign them aswell
                    $ucR->reset_users_all_clients($uiR, $cR, $ucS, $validator);
                    return $this->webService->getRedirectResponse('userinv/index');
                }
                if ((((string)$key === 'client_id'))){
                    $form_array = [
                        'user_id'=>$user_id,    
                        'client_id'=>$value
                    ];
                    $form = new UserClientForm();
                    if ($form->load($form_array) && $validator->validate($form)->isValid() && !$ucR->repoCheckNotExistClientIdquery($value) > 0){
                        $this->userclientService->saveUserClient(new UserClient(),$form);
                        $this->flash($session, 'warning' , $sR->trans('record_successfully_updated'));
                        return $this->webService->getRedirectResponse('userinv/index');
                    }
                    if ($ucR->repoCheckNotExistClientIdquery($value) > 0) {
                        $this->flash($session, 'info' , $sR->trans('client_already_exists'));
                        return $this->webService->getRedirectResponse('userinv/index');
                    }
                }
            }
        }        
        return $this->viewRenderer->render('new', $parameters);
    }
    
    public function view(SessionInterface $session, CurrentRoute $currentRoute, UserClientRepository $userclientRepository,
                         SettingRepository $settingRepository,
        ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('view'),
            'action' => ['userclient/view', ['id' => $this->userclient($currentRoute, $userclientRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->userclient($currentRoute, $userclientRepository)),
            's'=>$settingRepository,             
            'userclient'=>$userclientRepository->repoUserClientquery($this->userclient($currentRoute, $userclientRepository)->getId()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }
        
    private function rbac(SessionInterface $session) 
    {
        $canEdit = $this->userService->hasPermission('editUserClient');
        if (!$canEdit){
            $this->flash($session,'warning', $this->translator->translate('invoice.permission'));
            return $this->webService->getRedirectResponse('userclient/index');
        }
        return $canEdit;
    }
    
    private function user(CurrentRoute $currentRoute, UIR $uiR) 
    {
        $user_id = $currentRoute->getArgument('user_id');       
        $user = $uiR->repoUserInvUserIdquery($user_id);
        if ($user === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $user;
    }
    
    private function userclient(CurrentRoute $currentRoute,UserClientRepository $userclientRepository) 
    {
        //$id = $request->getAttribute('id');
        $id = $currentRoute->getArgument('id');       
        $userclient = $userclientRepository->repoUserClientquery((string)$id);
        if ($userclient === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $userclient;
    }
    
    private function userclients(UserClientRepository $userclientRepository) 
    {
        $userclients = $userclientRepository->findAllPreloaded();        
        if ($userclients === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $userclients;
    }
    
    private function body($userclient) {
        $body = [
                
          'id'=>$userclient->getId(),
          'user_id'=>$userclient->getUser_id(),
          'client_id'=>$userclient->getClient_id()
                ];
        return $body;
    }
    
    private function flash(SessionInterface $session, $level, $message){
        $flash = new Flash($session);
        $flash->set($level, $message); 
        return $flash;
    }
}

