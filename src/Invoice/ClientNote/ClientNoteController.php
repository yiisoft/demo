<?php

declare(strict_types=1); 

namespace App\Invoice\ClientNote;

use App\Invoice\Entity\ClientNote;
use App\Invoice\ClientNote\ClientNoteService;
use App\Invoice\ClientNote\ClientNoteRepository;
use App\Invoice\Setting\SettingRepository;
use App\Invoice\Client\ClientRepository;
use App\User\UserService;
use Yiisoft\Validator\ValidatorInterface;
use App\Service\WebControllerService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Http\Method;
use Yiisoft\Yii\View\ViewRenderer;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Session\Flash\Flash;

final class ClientNoteController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService;
    private ClientNoteService $clientnoteService;
    
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UserService $userService,
        ClientNoteService $clientnoteService
    )    
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/clientnote')
                                           ->withLayout(dirname(dirname(__DIR__)).'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->userService = $userService;
        $this->clientnoteService = $clientnoteService;
    }
    
    public function index(SessionInterface $session, ClientNoteRepository $clientnoteRepository, SettingRepository $settingRepository, Request $request, ClientNoteService $service): Response
    {
       
         $canEdit = $this->rbac($session);
         $flash = $this->flash($session, 'dummy' , 'Flash message!.');
         $parameters = [
      
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
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function edit(ViewRenderer $head, SessionInterface $session, Request $request, 
                        ValidatorInterface $validator,
                        ClientNoteRepository $clientnoteRepository, 
                        SettingRepository $settingRepository,                        
                        ClientRepository $clientRepository
    ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => 'Edit',
            'action' => ['clientnote/edit', ['id' => $this->clientnote($request, $clientnoteRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->clientnote($request, $clientnoteRepository)),
            'head'=>$head,
            's'=>$settingRepository,
                        'clients'=>$clientRepository->findAllPreloaded()
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new ClientNoteForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->clientnoteService->saveClientNote($this->clientnote($request,$clientnoteRepository), $form);
                return $this->webService->getRedirectResponse('clientnote/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function delete(SessionInterface $session,Request $request,ClientNoteRepository $clientnoteRepository 
    ): Response {
        $this->rbac($session);
       
        $this->clientnoteService->deleteClientNote($this->clientnote($request,$clientnoteRepository));               
        return $this->webService->getRedirectResponse('clientnote/index');        
    }
    
    public function view(SessionInterface $session,Request $request,ClientNoteRepository $clientnoteRepository,
        SettingRepository $settingRepository
        ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('view'),
            'action' => ['clientnote/edit', ['id' => $this->clientnote($request, $clientnoteRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->clientnote($request, $clientnoteRepository)),
            's'=>$settingRepository,             
            'clientnote'=>$clientnoteRepository->repoClientNotequery($this->clientnote($request, $clientnoteRepository)->getId()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }
    
    private function rbac(SessionInterface $session) 
    {
        $canEdit = $this->userService->hasPermission('editClientNote');
        if (!$canEdit){
            $this->flash($session,'warning', 'You do not have the required permission.');
            return $this->webService->getRedirectResponse('clientnote/index');
        }
        return $canEdit;
    }
    
    private function clientnote(Request $request,ClientNoteRepository $clientnoteRepository) 
    {
        $id = $request->getAttribute('id');       
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

?>