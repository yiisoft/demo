<?php

declare(strict_types=1); 

namespace App\Invoice\ClientNote;

use App\Invoice\Entity\ClientNote;
use App\Invoice\ClientNote\ClientNoteService;
use App\Invoice\ClientNote\ClientNoteRepository;
use App\Invoice\Setting\SettingRepository;
use App\Invoice\Client\ClientRepository;
use App\Invoice\Helpers\DateHelper;
use App\User\UserService;
use Yiisoft\Validator\ValidatorInterface;
use App\Service\WebControllerService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Http\Method;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Session\Flash\Flash;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Yii\View\ViewRenderer;

final class ClientNoteController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService;
    private ClientNoteService $clientnoteService;
    private TranslatorInterface $translator;
    
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UserService $userService,
        ClientNoteService $clientnoteService,
        TranslatorInterface $translator
    )    
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/clientnote')
                                           ->withLayout(dirname(dirname(__DIR__)).'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->userService = $userService;
        $this->clientnoteService = $clientnoteService;
        $this->translator = $translator;
    }
    
    public function index(SessionInterface $session, ClientNoteRepository $clientnoteRepository, DateHelper $dateHelper, SettingRepository $settingRepository, Request $request, ClientNoteService $service): Response
    {
         $canEdit = $this->rbac($session);
         $flash = $this->flash($session, '','');
         $parameters = [
          'd'=>$dateHelper,
          's'=>$settingRepository,
          'canEdit' => $canEdit,
          'clientnotes' => $this->clientnotes($clientnoteRepository),
          'flash'=> $flash
         ];

        if ($this->isAjaxRequest($request)) {
            return $this->viewRenderer->renderPartial('_clientnotes', ['data' => $paginator]);
        }
        
        return $this->viewRenderer->render('index', $parameters);
    }
    
    private function isAjaxRequest(Request $request): bool
    {
        return $request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
    }
    
    public function add(ViewRenderer $head,SessionInterface $session, Request $request, 
                        ValidatorInterface $validator,
                        DateHelper $dateHelper, 
                        SettingRepository $settingRepository,                        
                        ClientRepository $clientRepository
    )
    {
        $this->rbac($session);
        $parameters = [
            'title' => 'Add',
            'action' => ['clientnote/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            'd'=>$dateHelper,
            's'=>$settingRepository,
            'head'=>$head,
            'clients'=>$clientRepository->findAllPreloaded(),
        ];
        
        if ($request->getMethod() === Method::POST) {
            
            $form = new ClientNoteForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->clientnoteService->saveClientNote(new ClientNote(),$form);
                return $this->webService->getRedirectResponse('clientnote/index');
            }
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function edit(ViewRenderer $head, SessionInterface $session, Request $request, 
                        ValidatorInterface $validator,
                        ClientNoteRepository $clientnoteRepository, 
                        SettingRepository $settingRepository,                        
                        ClientRepository $clientRepository,
                        DateHelper $dateHelper, 
                        CurrentRoute $currentRoute
    ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => 'Edit',
            'action' => ['clientnote/edit', ['id' => $this->clientnote($currentRoute, $clientnoteRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->clientnote($currentRoute, $clientnoteRepository)),
            'head'=>$head,
            'd'=>$dateHelper,
            's'=>$settingRepository,
            'clients'=>$clientRepository->findAllPreloaded()
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new ClientNoteForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->clientnoteService->saveClientNote($this->clientnote($currentRoute,$clientnoteRepository), $form);
                return $this->webService->getRedirectResponse('clientnote/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function delete(SessionInterface $session, ClientNoteRepository $clientnoteRepository, CurrentRoute $currentRoute
    ): Response {
        $this->rbac($session);
       
        $this->clientnoteService->deleteClientNote($this->clientnote($currentRoute,$clientnoteRepository));               
        return $this->webService->getRedirectResponse('clientnote/index');        
    }
    
    public function view(SessionInterface $session, CurrentRoute $currentRoute, ClientNoteRepository $clientnoteRepository, DateHelper $dateHelper,
        SettingRepository $settingRepository
        ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('view'),
            'action' => ['clientnote/edit', ['id' => $this->clientnote($currentRoute, $clientnoteRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->clientnote($currentRoute, $clientnoteRepository)),
            'd'=>$dateHelper,
            's'=>$settingRepository,             
            'clientnote'=>$clientnoteRepository->repoClientNotequery($this->clientnote($currentRoute, $clientnoteRepository)->getId()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }
    
    private function rbac(SessionInterface $session) 
    {
        $canEdit = $this->userService->hasPermission('editClientNote');
        if (!$canEdit){
            $this->flash($session,'warning', $this->translator->translate('invoice.permission'));
            return $this->webService->getRedirectResponse('clientnote/index');
        }
        return $canEdit;
    }
    
    private function clientnote(CurrentRoute $currentRoute,ClientNoteRepository $clientnoteRepository) 
    {
        $id = $currentRoute->getArgument('id');       
        $clientnote = $clientnoteRepository->repoClientNotequery($id);
        if ($clientnote === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $clientnote;
    }
    
    private function clientnotes(ClientNoteRepository $clientnoteRepository) 
    {
        $clientnotes = $clientnoteRepository->findAllPreloaded();        
        if ($clientnotes === null) {
            return $this->webService->getNotFoundResponse();
        };
        return $clientnotes;
    }
    
    private function body($clientnote) {
        $body = [
                
          'id'=>$clientnote->getId(),
          'client_id'=>$clientnote->getClient_id(),
          'date'=>$clientnote->getDate(),
          'note'=>$clientnote->getNote()
                ];
        return $body;
    }
    
    private function flash(SessionInterface $session, $level, $message){
        $flash = new Flash($session);
        $flash->set($level, $message); 
        return $flash;
    }
}