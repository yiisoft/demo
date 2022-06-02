<?php
declare(strict_types=1);

namespace App\Invoice\Client;
// Entity's
use App\Invoice\Entity\Client;
use App\Invoice\Entity\UserClient;
use App\Invoice\Entity\ClientCustom;
// Services
use App\Service\WebControllerService;
use App\Invoice\ClientCustom\ClientCustomService;
use App\Invoice\ClientCustom\ClientCustomForm;
use App\Invoice\ClientNote\ClientNoteService as cnS;
use App\Invoice\UserClient\UserClientService;
use App\Invoice\UserClient\UserClientForm;
use App\User\UserService;
// Repositories
use App\Invoice\Client\ClientRepository as cR;
use App\Invoice\ClientCustom\ClientCustomRepository as ccR;
use App\Invoice\ClientNote\ClientNoteRepository as cnR;
use App\Invoice\CustomValue\CustomValueRepository as cvR;
use App\Invoice\CustomField\CustomFieldRepository as cfR;
use App\Invoice\InvAmount\InvAmountRepository as iaR;
use App\Invoice\Inv\InvRepository as iR;
use App\Invoice\Setting\SettingRepository as sR;
// Helpers
use App\Invoice\Helpers\CountryHelper;
use App\Invoice\Helpers\ClientHelper;
use App\Invoice\Helpers\CustomValuesHelper as CVH;
use App\Invoice\Helpers\DateHelper;
use App\Invoice\Helpers\GenerateCodeFileHelper;
// Psr\\Http
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
// Yii
use Yiisoft\Aliases\Aliases;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\DataResponse\DataResponseFactoryInterface;
use Yiisoft\Html\Html;
use Yiisoft\Http\Method;
use Yiisoft\Json\Json;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Session\Flash\Flash;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\User\CurrentUser;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\View\ViewRenderer;
// Miscellaneous
use \Exception;

final class ClientController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private ClientService $clientService;
    private ClientCustomService $clientCustomService;
    private UserService $userService;
    private UserClientService $userclientService;     
    private CurrentUser $currentUser;
    private DataResponseFactoryInterface $factory;
    private TranslatorInterface $translator;
    
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        ClientService $clientService,
        ClientCustomService $clientCustomService,
        UserService $userService,
        UserClientService $userclientService,
        CurrentUser $currentUser,
        DataResponseFactoryInterface $factory,
        TranslatorInterface $translator
    ) {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/client')
                                           ->withLayout(dirname(dirname(__DIR__)).'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->clientService = $clientService;
        $this->clientCustomService = $clientCustomService;
        $this->userService = $userService;
        $this->userclientService = $userclientService;
        $this->currentUser = $currentUser;
        $this->factory = $factory;
        $this->translator = $translator;
    }
    
    public function add(ViewRenderer $head,SessionInterface $session, Request $request, ValidatorInterface $validator, sR $sR): Response
    {
        $this->rbac($session);
        $aliases = new Aliases(['@invoice' => dirname(__DIR__), '@language' => '@invoice/Language']);
        $selected_country =  '';
        $selected_language = '';
        $countries = new CountryHelper();
        $parameters = [
            'title' => $sR->trans('add_client'),
            'action' => ['client/add'],
            'head'=>$head,
            'errors' => [],
            'body' => $request->getParsedBody(),
            'aliases'=> $aliases,
            's'=> $sR,
            'head'=> $head,
            'selected_country' => $selected_country ?: $sR->get_setting('default_country'),            
            'selected_language' => $selected_language ?: $sR->get_setting('default_language'),
            'countries'=> $countries->get_country_list($sR->get_setting('cldr'))
        ];
        
        if ($request->getMethod() === Method::POST) {            
            $form = new ClientForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $newclient = new Client();                
                // All new clients are made active in the clientService
                $this->clientService->saveClient($newclient,$form);
                // Assign all clients to administrator automatically
                if ($this->currentUser->getId() === '1') {
                    $user_client = [
                        'user_id'=>1,
                        'client_id'=>$newclient->getClient_id()
                    ];
                    $form = new UserClientForm();
                    if ($form->load($user_client) && $validator->validate($form)->isValid()) {
                        $this->userclientService->saveUserClient(new UserClient(), $form);
                    }
                }
                // Non-Administrators are assigned clients under Setting...User Account
                return $this->webService->getRedirectResponse('client/index');
            }
            $parameters['errors'] = $form->getFormErrors();
        }
        
        if ($this->isAjaxRequest($request)){                
                return $this->viewRenderer->renderPartial('__form', $parameters);                
        } else {
                return $this->viewRenderer->render('__form', $parameters);
        }
    }
    
    private function body($client) {        
        $body = [
                'client_date_created'=>$client->getClient_date_created(),
                'client_date_modified'=>$client->getClient_date_modified(),
                'client_name' => $client->getClient_name(),
                'client_address_1' => $client->getClient_address_1(),
                'client_address_2' => $client->getClient_address_2(),
                'client_city' => $client->getClient_city(),
                'client_state' => $client->getClient_state(),
                'client_zip' => $client->getClient_zip(),
                'client_country' => $client->getClient_country(),
                'client_phone' => $client->getClient_phone(),
                'client_fax' => $client->getClient_fax(),
                'client_mobile' => $client->getClient_mobile(),
                'client_email' => $client->getClient_email(),
                'client_web' => $client->getClient_web(),
                'client_vat_id' => $client->getClient_vat_id(),
                'client_tax_code' => $client->getClient_tax_code(),
                'client_language' => $client->getClient_language(),
                'client_active'=>$client->getClient_active(),
                'client_surname'=>$client->getClient_surname(),
                'client_avs' => $client->getClient_avs(),
                'client_insurednumber'=>$client->getClient_insurednumber(),
                'client_veka'=>$client->getClient_veka(),
                'client_birthdate'=>$client->getClient_birthdate(),
                'client_gender'=>$client->getClient_gender(),                
                ];
        return $body;
    }

    private function build_and_save($generated_dir_path,$content, $file,$name){
        $build_file = new GenerateCodeFileHelper("$generated_dir_path/$name$file", $content); 
        $build_file->save();
        return $build_file;
    }
    
    private function client(CurrentRoute $currentRoute,cR $cR) {
        $client_id = $currentRoute->getArgument('id');
        $client = $cR->repoClientquery($client_id);
        if ($client === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $client;
    }
    
    private function clients(cR $cR, $active) {
        $clients = $cR->findAllWithActive($active); 
        if ($clients === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $clients;
    }
    
    public function client_custom_values($client_id, ccR $ccR) : array
    {
        // Get all the custom fields that have been registered with this client on creation, retrieve existing values via repo, and populate 
        // custom_field_form_values array
        $custom_field_form_values = [];
        if ($ccR->repoClientCount((string)$client_id) > 0) {
            $client_custom_fields = $ccR->repoFields((string)$client_id);
            foreach ($client_custom_fields as $key => $val) {
                $custom_field_form_values['custom[' . $key . ']'] = $val;
            }
        }
        return $custom_field_form_values;
    }
    
    private function country($client)
    {
        return $client->getClient_country();
    }
    
    // Data fed from client.js->$(document).on('click', '#client_create_confirm', function () {
    public function create_confirm(SessionInterface $session, Request $request, ValidatorInterface $validator, cfR $cfR) : Response
    {
        $this->rbac($session);  
        $body = $request->getQueryParams() ?? [];
        $ajax_body = [
            'client_name'=>$body['client_name'],
            'client_email'=>$body['client_email'],
            'client_surname'=>$body['client_surname'],
            'client_gender'=>'', 
        ];
        $ajax_content = new ClientForm();
        if ($ajax_content->load($ajax_body) && $validator->validate($ajax_content)->isValid()) {  
            $newclient = new Client();
            $this->clientService->saveClient($newclient, $ajax_content);
                $client_id = $newclient->getClient_id();
                if ($this->currentUser->getId() === '1') {
                    $user_client = [
                        'user_id'=>1,
                        'client_id'=>$newclient->getClient_id()
                    ];
                    $form = new UserClientForm();
                    if ($form->load($user_client) && $validator->validate($form)->isValid()) {
                        $this->userclientService->saveUserClient(new UserClient(), $form);
                    }
                }
                // Get the custom fields that are mandatory for a client and initialise the first client with an empty value for each custom field
                $custom_fields = $cfR->repoTablequery('client_custom') ?? '';
                foreach($custom_fields as $custom_field){
                    $init_client_custom = new ClientCustomForm();
                    $client_custom = [];
                    $client_custom['client_id'] = $client_id;
                    $client_custom['custom_field_id'] = $custom_field->getId();
                    $client_custom['value'] = '';
                    ($init_client_custom->load($client_custom) && $validator->validate($init_client_custom)->isValid()) ? 
                    $this->clientCustomService->saveClientCustom(new ClientCustom(), $init_client_custom): '';
            }
            $parameters = [
               'success'=>1,
            ];
           //return response to client.js to reload page at location
            return $this->factory->createResponse(Json::encode($parameters));          
        } else {
            $parameters = [
               'success'=>0,
            ];
            //return response to client.js to reload page at location (DOM debugging)
            return $this->factory->createResponse(Json::encode($parameters));          
        } 
    }
    
    public function custom_fields(ValidatorInterface $validator, $body, $matches, $client_id, $ccR) : Response
    {   
        $parameters =[];
        if (!empty($body['custom'])) {
            $db_array = [];
            $values = [];
            foreach ($body['custom'] as $custom) {
                if (preg_match("/^(.*)\[\]$/i", $custom['name'], $matches)) {
                    $values[$matches[1]][] = $custom['value'];
                } else {
                    $values[$custom['name']] = $custom['value'];
                }
            }
            
            foreach ($values as $key => $value) {
                preg_match("/^custom\[(.*?)\](?:\[\]|)$/", $key, $matches);
                if ($matches) {
                    $db_array[$matches[1]] = $value;
                }
            }
            
            foreach ($db_array as $key => $value){
                $ajax_custom = new ClientCustomForm();
                $client_custom = [];
                $client_custom['client_id']=$client_id;
                $client_custom['custom_field_id']=$key;
                $client_custom['value']=$value; 
                $model = ($ccR->repoClientCustomCount($client_id,(string)$key) == 1 ? $ccR->repoFormValuequery($client_id,(string) $key) : new ClientCustom());
                ($ajax_custom->load($client_custom) && $validator->validate($ajax_custom)->isValid()) ? 
                        $this->clientCustomService->saveClientCustom($model, $ajax_custom) : '';                
            }
            $parameters = [
                'success'=>1,
            ];
            return $this->factory->createResponse(Json::encode($parameters)); 
        } else {
            $parameters = [
                'success'=>0,
            ];           
            return $this->factory->createResponse(Json::encode($parameters)); 
        }
    }  
    
    public function delete(SessionInterface $session,CurrentRoute $currentRoute,cR $cR
    ): Response {
        $this->rbac($session);
        try {
            $this->clientService->deleteClient($this->client($currentRoute, $cR));            
            //UserClient Entity automatically deletes the UserClient record relevant to this client 
            return $this->webService->getRedirectResponse('client/index');
	} catch (Exception $e) {
              unset($e);
              $this->flash($session, 'danger', 'Cannot delete. Client history exists.');
              return $this->webService->getRedirectResponse('client/index');
        }
    } 
    
    public function edit_custom(SessionInterface $session, Request $request, ccR $ccR, cR $cR, ValidatorInterface $validator, CurrentRoute $currentRoute) 
        : Response {
        // Accept the client.js data
        if ($this->isAjaxRequest($request)){
            $form = new ClientForm();
            $body = $request->getQueryParams() ?? [];
            // Testing:
            $this->build_and_save(dirname(dirname(dirname(__DIR__))).'/views/invoice/generator/output_overwrite', $body['custom'], 'body.php','');
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->clientService->saveClient($this->client($currentRoute, $cR), $form);
                $this->save_client_custom_fields($session, $validator, $ccR, $session->get('client_id'), $body['custom']);
                return $this->webService->getRedirectResponse('client/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFormErrors();
        }
        
    } 
    
    public function edit(ViewRenderer $head, SessionInterface $session, Request $request, cR $cR, ccR $ccR, cfR $cfR, cvR $cvR, 
            ValidatorInterface $validator,sR $sR, CurrentRoute $currentRoute
    ): Response {
        $this->rbac($session);
        $selected_country =  $this->country($this->client($currentRoute, $cR))  ?: '';
        $selected_language = $this->language($this->client($currentRoute, $cR)) ?: '';
        $countries = new CountryHelper();
        $client_id = $this->client($currentRoute, $cR)->getClient_id();
        $parameters = [
            'title' => $sR->trans('edit'),
            'action' => ['client/edit', ['id' => $client_id]],
            'errors' => [],
            'head'=>$head,
            'client'=>$this->client($currentRoute, $cR),
            'body' => $this->body($this->client($currentRoute, $cR)),
            'aliases'=> new Aliases(['@invoice' => dirname(__DIR__), '@language' => '@invoice/Language']),
            's'=>$sR,
            'selected_country' => $selected_country ?: $sR->get_setting('default_country'),            
            'selected_language' => $selected_language ?: $sR->get_setting('default_language'),
            'countries'=> $countries->get_country_list($sR->get_setting('cldr')),
            'custom_fields'=>$cfR->repoTablequery('client_custom'),
            'custom_values'=>$cvR->attach_hard_coded_custom_field_values_to_custom_field($cfR->repoTablequery('client_custom')),
            'client_custom_values'=>$this->client_custom_values($client_id, $ccR)
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new ClientForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->clientService->saveClient($this->client($currentRoute, $cR), $form);
                return $this->webService->getRedirectResponse('client/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFormErrors();
        }
        if ($this->isAjaxRequest($request)){                
                return $this->viewRenderer->renderPartial('__form', $parameters);                
        } else {
                return $this->viewRenderer->render('__form', $parameters);
        }
    }
    
    private function flash(SessionInterface $session, $level, $message){
        $flash = new Flash($session);
        $flash->set($level, $message); 
        return $flash;
    }
    
    public function index(CurrentRoute $currentRoute, SessionInterface $session, cR $cR, iaR $iaR, iR $iR, sR $sR): 
        Response
    {
        $canEdit = $this->rbac($session);
        $flash = $this->flash($session, 'success_or_info', 'Help information will appear here.');
        $pageNum = (int)$currentRoute->getArgument('page', '1');        
        $active = (int)$currentRoute->getArgument('active', '2');
        $paginator = (new OffsetPaginator($this->clients($cR, $active)))
            ->withPageSize((int)$sR->setting('default_list_limit'))
            ->withCurrentPage($pageNum);
        $parameters = [
            'paginator'=>$paginator,
            's'=> $sR,
            'iR'=> $iR,
            'iaR'=> $iaR,
            'canEdit' => $canEdit,
            'flash'=> $flash,
            'active'=>$active,
            'pageNum'=>$pageNum,
            'modal_create_client'=>$this->viewRenderer->renderPartialAsString('modal_create_client',[
                    's'=>$sR,
                    'datehelper'=> new DateHelper($sR)
            ])
        ];    
        return $this->viewRenderer->render('index', $parameters);
    }
       
    
    /**
     * Get the latest clients
     */
    public function get_latest(cR $cR) : Response
    {
        $clienthelper = new ClientHelper();
        $clients = $cR->repoClientLatest();
        foreach ($clients as $client) {
            $parameters[] = [
                'id' => $client->client_id,
                'text' => Html::encode($clienthelper->format_client($client)),
            ];
        }
        // Return the results
        return $this->factory->createResponse(Json::encode($parameters));  
    }
    
    private function isAjaxRequest(Request $request): bool
    {
        return $request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
    }
    
    private function language($client)
    {
        return $client->getClient_language();
    }
    
    public function load_client_notes(Request $request, cnR $cnR)
    {
        $body = $request->getQueryParams() ?? [];
        $client_id = $body['client_id'];
        $data = [
            'client_notes' => $cnR->repoClientquery($client_id)
        ];

        return $this->viewRenderer->render('partial_notes', $data);
    }
       
    
    public function namequery(Request $request, cR $cR) : Response
    {
        $query = $request->getQueryParams() ?? [];
        $permissiveSearchClients = $query['permissive_search_clients'];

        if (empty($query)) {
            return $this->factory->createResponse(Json::encode([]));  
        }

        // Search for chars "in the middle" of clients names
        $permissiveSearchClients ? $moreClientsQuery = '%' : '';

        // Search for clients
        $escapedQuery = str_replace("%", "", $query);
        
        $clients = $cR->repoClientSearch($moreClientsQuery, $escapedQuery);
        $clienthelper = new ClientHelper();
        foreach ($clients as $client) {
            $parameters = [
                'id' => $client->client_id,
                'text' => Html::encode($clienthelper->format_client($client)),
            ];
        }

        // Return the results
        return $this->factory->createResponse(Json::encode($parameters));  
    }
    
    private function rbac(SessionInterface $session) {
        $canEdit = $this->userService->hasPermission('editClient');
        if (!$canEdit){
            $this->flash($session,'warning', $this->translator->generate('invoice.permission'));
            return $this->webService->getRedirectResponse('client/index');
        }
        return $canEdit;
    }
    
    // save the client custom fields
    public function save_client_custom_fields($session, $validator, $ccR, $client_id, $body)
                    : Response
    {
       $parameters = [];
       $parameters['success'] = 0; 
       $this->rbac($session);
       $custom = $body['custom'] ? $body['custom'] : '';
       $custom_field_body = [            
            'custom'=>$custom,            
       ];
       if (!empty($custom_field_body['custom'])) {
            $db_array = [];
            $values = [];
            foreach ($custom_field_body['custom'] as $custom) {
                if (preg_match("/^(.*)\[\]$/i", $custom['name'], $matches)) {
                    $values[$matches[1]][] = $custom['value'];
                } else {
                    $values[$custom['name']] = $custom['value'];
                }
            }            
            foreach ($values as $key => $value) {
                preg_match("/^custom\[(.*?)\](?:\[\]|)$/", $key, $matches);
                if ($matches) {
                    $db_array[$matches[1]] = $value;
                }
            }            
            foreach ($db_array as $key => $value){
                $ajax_custom = new ClientCustomForm();
                $client_custom = [];
                $client_custom['client_id']=$client_id;
                $client_custom['custom_field_id']=$key;
                $client_custom['value']=$value; 
                $model = ($ccR->repoClientCustomCount($client_id,(string)$key) == 1 ? $ccR->repoFormValuequery($client_id,(string) $key) : new ClientCustom());
                ($ajax_custom->load($client_custom) && $validator->validate($ajax_custom)->isValid()) ? 
                        $this->clientCustomService->saveClientCustom($model, $ajax_custom) : '';                
            }
            $parameters = [
                'success'=>1,
                'clientid'=>$client_id,
            ];
            return $this->factory->createResponse(Json::encode($parameters)); 
        } else {
            $parameters = [
                'success'=>0,
            ];           
            return $this->factory->createResponse(Json::encode($parameters)); 
        }
    }
    
    // save the client custom fields
     public function save_custom_fields(SessionInterface $session, ValidatorInterface $validator, Request $request, ccR $ccR)
                    : Response
    {
       $parameters = [];      
       $parameters['success'] = 0; 
       $this->rbac($session);       
       $body = $request->getQueryParams() ?? [];
       $custom = $body['custom'] ? $body['custom'] : '';
       $custom_field_body = [            
            'custom'=>$custom,            
        ];      
       $client_id = $session->get('client_id');
       if (!empty($custom_field_body['custom'])) {
            $db_array = [];
            $values = [];
            foreach ($custom_field_body['custom'] as $custom) {
                if (preg_match("/^(.*)\[\]$/i", $custom['name'], $matches)) {
                    $values[$matches[1]][] = $custom['value'];
                } else {
                    $values[$custom['name']] = $custom['value'];
                }
            }            
            foreach ($values as $key => $value) {
                preg_match("/^custom\[(.*?)\](?:\[\]|)$/", $key, $matches);
                if ($matches) {
                    $db_array[$matches[1]] = $value;
                }
            }            
            foreach ($db_array as $key => $value){
                $ajax_custom = new ClientCustomForm();
                $client_custom = [];
                $client_custom['client_id']=$client_id;
                $client_custom['custom_field_id']=$key;
                $client_custom['value']=$value; 
                $model = ($ccR->repoClientCustomCount($client_id, (string)$key) == 1 ? $ccR->repoFormValuequery($client_id, (string)$key) : new ClientCustom());
                ($ajax_custom->load($client_custom) && $validator->validate($ajax_custom)->isValid()) ? 
                        $this->clientCustomService->saveClientCustom($model, $ajax_custom) : '';                
            }
            $parameters = [
                'success'=>1,                
            ];
            return $this->factory->createResponse(Json::encode($parameters)); 
        } else {
            $parameters = [
                'success'=>0,
            ];           
            return $this->factory->createResponse(Json::encode($parameters)); 
        }
    }

    public function save_client_note(Request $request, SessionInterface $session, Validator $validator, cnS $cnS, cnR $cnR, sR $sR)
    {
      if ($this->isAjaxRequest($request)) { 
        $this->rbac($session);
        $parameters['success']=0;
        //receive data ie. note
        $body = $request->getQueryParams() ?? [];
        $id = $body['id'];
        $client_id = $body['client_id'];
        $date = $body['date'];
        $note = $body['note'];
        $clientnote = $cnR->repoQuotequery($id);
        $data = [
            'id'=>$id,                
            'client_id'=>$client_id,
            'date'=>$date,
            'note'=>$note,
        ];
        $content = new ClientNoteForm();
        
        if ($content->load($data) && $validator->validate($content)->isValid()) {    
            $cnS->saveClientNote($this->userService->getUser(),$clientnote, $content,$sR);
            $parameters = [
                'success' => 1,
            ];
        } else {
            $parameters = [
                'success' => 0,
                'validation_errors' => $content->getFirstErrors()
            ];
        }        
        return $this->factory->createResponse(Json::encode($parameters));  
      }  
    }
        
    public function view(SessionInterface $session, CurrentRoute $currentRoute, cR $cR, sR $sR   
    ): Response {
        $this->rbac($session);
        $selected_country =  $this->country($this->client($currentRoute, $cR))  ?: '';
        $selected_language = $this->language($this->client($currentRoute, $cR)) ?: '';
        $countries = new CountryHelper();
        $parameters = [
            'title' => $sR->trans('edit_client'),
            'action' => ['client/view', ['id' => $this->client($currentRoute, $cR)->getClient_id()]],
            'errors' => [],
            'client'=>$this->client($currentRoute, $cR),
            'body' => $this->body($this->client($currentRoute, $cR)),
            'aliases'=>new Aliases(['@invoice' => dirname(__DIR__), '@language' => '@invoice/Language']),
            's'=>$sR,
            'selected_country' => $selected_country ?: $sR->get_setting('default_country'),            
            'selected_language' => $selected_language ?: $sR->get_setting('default_language'),
            'countries'=> $countries->get_country_list($sR->get_setting('cldr'))
        ];
        return $this->viewRenderer->render('__view', $parameters);
    } 
    
    public function view_client_custom_fields(ViewRenderer $head, SessionInterface $session, CurrentRoute $currentRoute,
                         cfR $cfR, cvR $cvR, cR $cR, ccR $ccR, sR $sR) : Response {
        $this->rbac($session);
        $session->set('client_id', (string)$this->client($currentRoute, $cR, false)->getClient_Id());
        $client_custom_values = $this->client_custom_values((string)$session->get('client_id'),$ccR);
        $selected_country =  $this->country($this->client($currentRoute, $cR))  ?: '';
        $selected_language = $this->language($this->client($currentRoute, $cR)) ?: '';
        $countries = new CountryHelper();
        $client_id = $this->client($currentRoute, $cR)->getClient_id();
        $parameters = [
            'title' => $sR->trans('view'),
            's'=>$sR,            
            'edit_client'=>$this->viewRenderer->renderPartialAsString('__form', [
                    'title' => $sR->trans('edit'),
                    'action' => ['client/edit', ['id' => $client_id]],
                    'errors' => [],
                    'head'=>$head,
                    'client'=>$this->client($currentRoute, $cR),
                    'body' => $this->body($this->client($currentRoute, $cR)),
                    'aliases'=> new Aliases(['@invoice' => dirname(__DIR__), '@language' => '@invoice/Language']),
                    's'=>$sR,
                    'selected_country' => $selected_country ?: $sR->get_setting('default_country'),            
                    'selected_language' => $selected_language ?: $sR->get_setting('default_language'),
                    'countries'=> $countries->get_country_list($sR->get_setting('cldr')),
                    'custom_fields'=>$cfR->repoTablequery('client_custom'),
                    'custom_values'=>$cvR->attach_hard_coded_custom_field_values_to_custom_field($cfR->repoTablequery('client_custom')),
                    'client_custom_values'=>$this->client_custom_values($client_id, $ccR)
            ]),
            'client_custom_fields'=>$this->viewRenderer->renderPartialAsString('/invoice/client/_client_custom_fields', [
                    's'=>$sR,
                    'action' => ['customfield/index'],
                    'client_id'=>$session->get('client_id'),
                    'fields' => $ccR->repoFields($session->get('client_id')),
                    // Get the standard extra custom fields built for your client. 
                    'custom_fields'=>$cfR->repoTablequery('client_custom'),
                    'custom_values'=>$cvR->attach_hard_coded_custom_field_values_to_custom_field($cfR->repoTablequery('client_custom')),
                    // Use the cvh to build the fields
                    'cvH'=> new CVH($sR),
                    'client_custom_values' => $this->client_custom_values($session->get('client_id'),$ccR),
                    // Use the head value to generate a save button
                    'head'=>$head
            ]),
            // Get all the fields that have been setup for this SPECIFIC client in client_custom. 
            'fields' => $ccR->repoFields($session->get('client_id')),
            // Get the standard extra custom fields built for EVERY client. 
            'custom_fields'=>$cfR->repoTablequery('client_custom'),
            'custom_values'=>$cvR->attach_hard_coded_custom_field_values_to_custom_field($cfR->repoTablequery('client_custom')),
            'cvH'=> new CVH($sR),
            'client_custom_values' => $client_custom_values,
            'client'=>$cR->repoClientquery($session->get('client_id')),
            'view_custom_fields'=>$this->viewRenderer->renderPartialAsString('view_custom_fields', [
                     'custom_fields'=>$cfR->repoTablequery('client_custom'),
                     'custom_values'=>$cvR->attach_hard_coded_custom_field_values_to_custom_field($cfR->repoTablequery('client_custom')),
                     'client_custom_values'=> $client_custom_values,  
                     'cvH'=> new CVH($sR),
                     's'=>$sR,   
            ]),        
        ];
        return $this->viewRenderer->render('view', $parameters);
    }  
}
