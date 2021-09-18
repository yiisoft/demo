<?php

declare(strict_types=1);

namespace App\Invoice\Client;

use App\Invoice\Entity\Client;
use App\Invoice\Entity\UserClient;
use App\Invoice\Helpers\CountryHelper;
use App\Invoice\Setting\SettingRepository;
use App\Service\WebControllerService;
use App\User\UserService;
use App\Invoice\UserClient\UserClientService;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Http\Method;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\View\ViewRenderer;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Session\Flash\Flash;
use Yiisoft\User\CurrentUser;
use \Exception;

final class ClientController
{
    private const CLIENTS_PER_PAGE = 5; 
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private ClientService $clientService;
    private UserService $userService;
    private UserClientService $userclientService;     
    private CurrentUser $currentUser;

    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        ClientService $clientService,
        UserService $userService,
        UserClientService $userclientService,
        CurrentUser $currentUser             
    ) {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/client')
                                           ->withLayout(dirname(dirname(__DIR__)).'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->clientService = $clientService;
        $this->userService = $userService;
        $this->userclientService = $userclientService;
        $this->currentUser = $currentUser;
    }

    public function index(Request $request, ViewRenderer $head,SessionInterface $session, ClientRepository $clientRepository, SettingRepository $settingRepository): 
        Response
    {
        $canEdit = $this->rbac($session);
        $flash = $this->flash($session, 'success', 'Help information will appear here.');
        $pageNum = (int)$request->getAttribute('page', 1);
        $paginator = (new OffsetPaginator($this->clients($clientRepository)))
            ->withPageSize(self::CLIENTS_PER_PAGE)
            ->withCurrentPage($pageNum);
        $parameters = [
            'paginator'=>$paginator,
            's'=> $settingRepository,
            'canEdit' => $canEdit,
            'flash'=> $flash
        ];    
        return $this->viewRenderer->render('index', $parameters);
    }

    public function add(SessionInterface $session, Request $request, ValidatorInterface $validator, SettingRepository $settingRepository): Response
    {
        $this->rbac($session);
        $aliases = new Aliases(['@invoice' => dirname(__DIR__), '@language' => '@invoice/Language']);
        $selected_country =  '';
        $selected_language = '';
        $countries = new CountryHelper();
        $parameters = [
            'title' => $settingRepository->trans('add_client'),
            'action' => ['client/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            'aliases'=>$aliases,
            's'=>$settingRepository,
            'selected_country' => $selected_country ?: $settingRepository->get_setting('default_country'),            
            'selected_language' => $selected_language ?: $settingRepository->get_setting('default_language'),
            'countries'=> $countries->get_country_list($settingRepository->get_setting('cldr'))
        ];
        
        if ($request->getMethod() === Method::POST) {
            
            $form = new ClientForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $newclient = new Client();
                $this->clientService->saveClient($newclient,$form);
                $this->userclientService->saveUserClient(new UserClient(),$this->currentUser->getId(),$newclient->getClient_id());
                return $this->webService->getRedirectResponse('client/index');
            }
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('__form', $parameters);
    }

    public function edit(SessionInterface $session, Request $request, ClientRepository $clientRepository, ValidatorInterface $validator,SettingRepository $settingRepository
    ): Response {
        $this->rbac($session);
        $selected_country =  $this->country($this->client($request, $clientRepository))  ?: '';
        $selected_language = $this->language($this->client($request, $clientRepository)) ?: '';
        $countries = new CountryHelper();
        $parameters = [
            'title' => $settingRepository->trans('edit'),
            'action' => ['client/edit', ['id' => $this->client($request, $clientRepository)->getClient_id()]],
            'errors' => [],
            'client'=>$this->client($request, $clientRepository),
            'body' => $this->body($this->client($request, $clientRepository)),
            'aliases'=> new Aliases(['@invoice' => dirname(__DIR__), '@language' => '@invoice/Language']),
            's'=>$settingRepository,
            'selected_country' => $selected_country ?: $settingRepository->get_setting('default_country'),            
            'selected_language' => $selected_language ?: $settingRepository->get_setting('default_language'),
            'countries'=> $countries->get_country_list($settingRepository->get_setting('cldr'))
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new ClientForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->clientService->saveClient($this->client($request,$clientRepository), $form);
                return $this->webService->getRedirectResponse('client/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('__form', $parameters);
    }
    
    public function delete(SessionInterface $session,Request $request,ClientRepository $clientRepository
    ): Response {
        $this->rbac($session);
        try {
            $this->clientService->deleteClient($this->client($request, $clientRepository));            
            //UserClient Entity automatically deletes the UserClient record relevant to this client 
            return $this->webService->getRedirectResponse('client/index');
	} catch (Exception $e) {
              unset($e);
              $this->flash($session, 'danger', 'Cannot delete. Client history exists.');
              return $this->webService->getRedirectResponse('client/index');
        }
    }
    
    public function view(SessionInterface $session, Request $request, ClientRepository $clientRepository, ValidatorInterface $validator,SettingRepository $settingRepository   
    ): Response {
        $this->rbac($session);
        $selected_country =  $this->country($this->client($request, $clientRepository))  ?: '';
        $selected_language = $this->language($this->client($request, $clientRepository)) ?: '';
        $countries = new CountryHelper();
        $parameters = [
            'title' => $settingRepository->trans('edit_client'),
            'action' => ['client/edit', ['id' => $this->client($request, $clientRepository)->getClient_id()]],
            'errors' => [],
            'client'=>$this->client($request, $clientRepository),
            'body' => $this->body($this->client($request, $clientRepository)),
            'aliases'=>new Aliases(['@invoice' => dirname(__DIR__), '@language' => '@invoice/Language']),
            's'=>$settingRepository,
            'selected_country' => $selected_country ?: $settingRepository->get_setting('default_country'),            
            'selected_language' => $selected_language ?: $settingRepository->get_setting('default_language'),
            'countries'=> $countries->get_country_list($settingRepository->get_setting('cldr'))
        ];
        return $this->viewRenderer->render('__view', $parameters);
    }
    
    private function rbac(SessionInterface $session) {
        $canEdit = $this->userService->hasPermission('editClient');
        if (!$canEdit){
            $this->flash($session,'warning', 'You do not have the required permission.');
            return $this->webService->getRedirectResponse('client/index');
        }
        return $canEdit;
    }
    
    private function client(Request $request,ClientRepository $clientRepository) {
        $client_id = $request->getAttribute('id');
        $client = $clientRepository->repoClientquery($client_id);
        if ($client === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $client;
    }
    
    private function clients(ClientRepository $clientRepository) {
           $clients = $clientRepository->findAllPreloaded(); 
        if ($clients === null) {
            return $this->webService->getNotFoundResponse();
        };
        return $clients;
    }
    
    private function country($client)
    {
        return $client->getClient_country();
    }
    
    private function language($client)
    {
        return $client->getClient_language();
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
                'client_gender'=>$client->getClient_gender()
                ];
        return $body;
    }
    
    private function flash(SessionInterface $session, $level, $message){
        $flash = new Flash($session);
        $flash->set($level, $message); 
        return $flash;
    }
}
