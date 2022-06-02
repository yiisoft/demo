<?php
declare(strict_types=1); 

namespace App\Invoice\UserInv;

use App\Invoice\Entity\UserInv;
use App\Invoice\Helpers\CountryHelper;
use App\Invoice\UserInv\UserInvService;
use App\Invoice\Client\ClientRepository;
use App\Invoice\UserInv\UserInvRepository;
use App\Invoice\UserClient\UserClientRepository;
use App\Invoice\Setting\SettingRepository;
use App\User\UserRepository as uR;
use App\User\UserService;
use App\Service\WebControllerService;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Yiisoft\Aliases\Aliases;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\DataResponse\DataResponseFactoryInterface;
use Yiisoft\Http\Method;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Session\Flash\Flash;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\View\ViewRenderer;

use \Exception;

final class UserInvController
{
    private DataResponseFactoryInterface $factory;
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService;
    private UserInvService $userinvService;
    private TranslatorInterface $translator;
        
    public function __construct(
        DataResponseFactoryInterface $factory,
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UserService $userService,
        UserInvService $userinvService,
        TranslatorInterface $translator
    )    
    {
        $this->factory = $factory;
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/userinv')
                                           ->withLayout(dirname(dirname(__DIR__)).'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->userService = $userService;
        $this->userinvService = $userinvService;
        $this->translator = $translator;
    }
    
    public function index(Request $request, CurrentRoute $currentRoute, SessionInterface $session,
                          UserInvRepository $uiR, SettingRepository $sR, TranslatorInterface $translator): Response
    {      
         $query_params = $request->getQueryParams() ?? [];
         $canEdit = $this->rbac($session);
         $flash = $this->flash($session, '','');
         $pageNum = (int)$currentRoute->getArgument('page', '1');        
         $active = (int)$currentRoute->getArgument('active', '2');         
         $sort = Sort::only(['user_id', 'name', 'email'])->withOrderString($query_params['sort'] ?? ''); 
         $repo = $this->userinvs_active_with_sort($uiR,$active,$sort); 
         $paginator = (new OffsetPaginator($repo))        
            ->withPageSize((int)$sR->setting('default_list_limit'))
            ->withCurrentPage($pageNum);
         $parameters = [
          'uiR'=>$uiR,
          'active'=>$active,   
          'paginator'=>$paginator,
          'translator'=>$translator,
          's'=>$sR,
          'canEdit' => $canEdit,
          'userinvs' => $repo,
          'locale'=>$session->get('_language'),
          'flash'=> $flash,
          // Parameters for GridView->requestArguments
          'page'=> $pageNum,
          'sortOrder' => $query_params['sort'] ?? '',
        ];
        return $this->viewRenderer->render('index', $parameters);
        
    }
    
    private function isAjaxRequest(Request $request): bool
    {
        return $request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
    }
    
    public function add(ViewRenderer $head,SessionInterface $session, Request $request, 
                        ValidatorInterface $validator,
                        SettingRepository $sR,
                        uR $uR, 
    ) : Response
    {
        $this->rbac($session);        
        $aliases = new Aliases(['@invoice' => dirname(__DIR__), '@language' => '@invoice/Language']);
        $countries = new CountryHelper();
        $selected_country =  '';
        $selected_language = '';
        $parameters = [
            'title' => $sR->trans('add'),
            'action' => ['userinv/add'],
            'aliases'=>$aliases,
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$sR,
            'head'=>$head,            
            'users'=>$uR->findAll(),
            'selected_country' => $selected_country ?: $sR->get_setting('default_country'),            
            'selected_language' => $selected_language ?: $sR->get_setting('default_language'),
            'countries'=> $countries->get_country_list($sR->get_setting('cldr'))
        ];
        
        if ($request->getMethod() === Method::POST) {            
            $form = new UserInvForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->userinvService->saveUserInv(new UserInv(),$form);
                return $this->webService->getRedirectResponse('userinv/index');
            }
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function edit(ViewRenderer $head, SessionInterface $session, Request $request, CurrentRoute $currentRoute, 
                        ValidatorInterface $validator,
                        UserInvRepository $userinvRepository, 
                        SettingRepository $settingRepository,
                        uR $uR,

    ): Response {
        $this->rbac($session);
        $aliases = new Aliases(['@invoice' => dirname(__DIR__), '@language' => '@invoice/Language']);
        $parameters = [
            'title' => 'Edit',
            'action' => ['userinv/edit', ['id' => $this->userinv($currentRoute, $userinvRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->userinv($currentRoute, $userinvRepository)),
            'head'=>$head,
            'aliases'=>$aliases,
            'users'=>$uR->findAll(),
            's'=>$settingRepository,
            
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new UserInvForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->userinvService->saveUserInv($this->userinv($currentRoute,$userinvRepository), $form);
                return $this->webService->getRedirectResponse('userinv/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function client(ViewRenderer $head, CurrentRoute $currentRoute, SessionInterface $session, ClientRepository $cR,
                           SettingRepository $sR, UserClientRepository $ucR, UserInvRepository $uiR) : Response {
        // Use the primary key 'id' passed in userinv/index's urlGenerator to retrieve the user_id
        $user_id = $this->userinv($currentRoute, $uiR)->getUser_Id();
        $parameters = [
            'head'=>$head,
            's'=>$sR,
            'cR'=>$cR,
            'flash'=> $this->flash($session, '', ''),
            // Get all clients that this user will deal with
            'user_clients'=>$ucR->repoClientquery($user_id),
            'userinv'=>$uiR->repoUserInvUserIdquery($user_id),
            'user_id'=>$user_id,
        ];
        return $this->viewRenderer->render('field', $parameters);
    }
    
    public function delete(SessionInterface $session, TranslatorInterface $translator, CurrentRoute $currentRoute,UserInvRepository $userinvRepository 
    ): Response {
        $this->rbac($session);
        try {
            $this->userinvService->deleteUserInv($this->userinv($currentRoute,$userinvRepository));               
            $this->flash($session, 'info', $translator->translate('invoice.deleted'));
            return $this->webService->getRedirectResponse('userinv/index'); 
	} catch (Exception $e) {
            //unset($e);
            $this->flash($session, 'danger', $e);
            return $this->webService->getRedirectResponse('userinv/index'); 
        }
    }
    
    public function view(SessionInterface $session, CurrentRoute $currentRoute,UserInvRepository $userinvRepository,
        SettingRepository $settingRepository,
        ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('view'),
            'action' => ['userinv/view', ['id' => $this->userinv($currentRoute, $userinvRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->userinv($currentRoute, $userinvRepository)),
            's'=>$settingRepository,             
            'userinv'=>$userinvRepository->repoUserInvquery($this->userinv($currentRoute, $userinvRepository)->getId()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }
        
    private function rbac(SessionInterface $session) 
    {
        $canEdit = $this->userService->hasPermission('editUserInv');
        if (!$canEdit){
            $this->flash($session,'warning', $this->translator->translate('invoice.permission'));
            return $this->webService->getRedirectResponse('userinv/index');
        }
        return $canEdit;
    }
    
    private function userinv(CurrentRoute $currentRoute, UserInvRepository $userinvRepository) 
    {
        //$id = $request->getAttribute('id');
        $id = $currentRoute->getArgument('id');       
        $userinv = $userinvRepository->repoUserInvquery($id);
        if ($userinv === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $userinv;
    }
    
    private function userinvs_filter(UserInvRepository $uiR, $filter) {  
        $userinvs = $uiR->
        $userinvs = $uiR->getReaderSortFilter()->withFilter($filter);
        if ($userinvs === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $userinvs;
    }
    
    private function userinvs_active_with_sort(UserInvRepository $uiR, $active, $sort) {       
        $userinvs = $uiR->findAllWithActive($active)
                        ->withSort($sort);
        if ($userinvs === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $userinvs;
    }
    
    private function userinvs_active_with_filter(UserInvRepository $uiR, $active, $name_is_filter) {       
        $userinvs = $uiR->findAllWithActive($active)
                        ->withFilter($name_is_filter);
        if ($userinvs === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $userinvs;
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
    
    private function body($userinv) {
        $body = [
                
          'id'=>$userinv->getId(),
          'user_id'=>$userinv->getUser_id(),
          'type'=>$userinv->getType(),
          'active'=>$userinv->getActive(),
          'date_created'=>$userinv->getDate_created(),
          'date_modified'=>$userinv->getDate_modified(),
          'language'=>$userinv->getLanguage(),
          'name'=>$userinv->getName(),
          'company'=>$userinv->getCompany(),
          'address_1'=>$userinv->getAddress_1(),
          'address_2'=>$userinv->getAddress_2(),
          'city'=>$userinv->getCity(),
          'state'=>$userinv->getState(),
          'zip'=>$userinv->getZip(),
          'country'=>$userinv->getCountry(),
          'phone'=>$userinv->getPhone(),
          'fax'=>$userinv->getFax(),
          'mobile'=>$userinv->getMobile(),
          'email'=>$userinv->getEmail(),
          'password'=>$userinv->getPassword(),
          'web'=>$userinv->getWeb(),
          'vat_id'=>$userinv->getVat_id(),
          'tax_code'=>$userinv->getTax_code(),
          'all_clients'=>$userinv->getAll_clients(),
          'salt'=>$userinv->getSalt(),
          'passwordreset_token'=>$userinv->getPasswordreset_token(),
          'subscribernumber'=>$userinv->getSubscribernumber(),
          'iban'=>$userinv->getIban(),
          'gln'=>$userinv->getGln(),
          'rcc'=>$userinv->getRcc()
                ];
        return $body;
    }
    
    private function flash(SessionInterface $session, $level, $message){
        $flash = new Flash($session);
        $flash->set($level, $message); 
        return $flash;
    }
}

