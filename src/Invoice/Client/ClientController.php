<?php

declare(strict_types=1);

namespace App\Invoice\Client;

use App\Invoice\Entity\Client;
use App\Invoice\Helpers\CountryHelper;
use App\Invoice\Setting\SettingRepository;
use App\Service\WebControllerService;
use App\User\UserService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Http\Method;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\View\ViewRenderer;
use Yiisoft\Aliases\Aliases;

final class ClientController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private ClientService $clientService;
    private UserService $userService;

    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        ClientService $clientService,
        UserService $userService
    ) {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/client');
        $this->webService = $webService;
        $this->clientService = $clientService;
        $this->userService = $userService;
    }

    public function index(Request $request, ClientRepository $clientRepository): Response
    {
        $canEdit = $this->userService->hasPermission('editClient');
        $clients = $clientRepository->findAllPreloaded();        
        if ($clients === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $this->viewRenderer->render('index', [ 'canEdit' => $canEdit, 'clients' => $clients]);
    }

    public function add(Request $request, ValidatorInterface $validator, SettingRepository $settingRepository): Response
    {
        $aliases = new Aliases(['@invoice' => dirname(__DIR__), '@language' => '@invoice/Language']);
        $language = $aliases->get('@language');
        //todo client_country
        $selected_country = $request->getParsedBody();
        $countries = new CountryHelper();
        $parameters = [
            'title' => $settingRepository->trans('add_client'),
            'action' => ['client/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            'aliases'=>$aliases,
            's'=>$settingRepository,
            'selected_country' => $selected_country ?: $settingRepository->get_setting('default_country'),
            'countries'=> $countries->get_country_list($settingRepository->get_setting('cldr'))
        ];
        
        if ($request->getMethod() === Method::POST) {
            
            $form = new ClientForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->clientService->saveClient($this->userService->getUser(),new Client(),$form);
                return $this->webService->getRedirectResponse('client/index');
            }
            $parameters['errors'] = $form->getFirstErrors();
        }

        return $this->viewRenderer->render('__form', $parameters, );
    }

    public function edit(Request $request,ClientRepository $clientRepository,ValidatorInterface $validator,SettingRepository $settingRepository    
    ): Response {
        $client_id = $request->getAttribute('client_id');
        $canEdit = $this->userService->hasPermission('editClient');
        $client = $clientRepository->repoClientquery($client_id);
        if ($client === null) {
            return $this->webService->getNotFoundResponse();
        }
        if (!$canEdit){
            //improve with flashmessage later
            return $this->webService->getRedirectResponse('client/index');
        }
        $aliases = new Aliases(['@invoice' => dirname(__DIR__), '@language' => '@invoice/Language']);
        $language = $aliases->get('@language');
        //todo client_country
        $selected_country = $request->getParsedBody();
        $countries = new CountryHelper();
        $parameters = [
            'title' => $settingRepository->trans('edit_client'),
            'action' => ['client/edit', ['client_id' => $client_id]],
            'errors' => [],
            'client'=>$client,
            'body' => [
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
            ],
            'aliases'=>$aliases,
            's'=>$settingRepository,
            'selected_country' => $selected_country ?: $settingRepository->get_setting('default_country'),
            'countries'=> $countries->get_country_list($settingRepository->get_setting('cldr'))
        ];

        if ($request->getMethod() === Method::POST) {
            $form = new ClientForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->clientService->saveClient($this->userService->getUser(),$client, $form);
                return $this->webService->getRedirectResponse('client/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFirstErrors();
        }

        return $this->viewRenderer->render('__form', $parameters);
    }
}
