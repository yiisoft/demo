<?php

declare(strict_types=1); 

namespace App\Invoice\Recurring;

use App\Invoice\Entity\Recurring;
use App\Invoice\Recurring\RecurringService;
use App\Invoice\Recurring\RecurringRepository;
use App\Invoice\Setting\SettingRepository;
use App\Invoice\Inv\InvRepository;
use App\User\UserService;
use Yiisoft\Validator\ValidatorInterface;
use App\Service\WebControllerService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Http\Method;
use Yiisoft\Yii\View\ViewRenderer;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Session\Flash\Flash;

final class RecurringController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService;
    private RecurringService $recurringService;
    
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UserService $userService,
        RecurringService $recurringService
    )    
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/recurring')
                                           ->withLayout(dirname(dirname(__DIR__)) .'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->userService = $userService;
        $this->recurringService = $recurringService;
    }
    
    public function index(SessionInterface $session, RecurringRepository $recurringRepository, SettingRepository $settingRepository, Request $request, RecurringService $service): Response
    {
       
         $canEdit = $this->rbac($session);
         $flash = $this->flash($session, 'dummy' , 'Flash message!.');
         $parameters = [
      
          's'=>$settingRepository,
          'canEdit' => $canEdit,
          'recurrings' => $this->recurrings($recurringRepository),
          'flash'=> $flash
         ];

        if ($this->isAjaxRequest($request)) {
            return $this->viewRenderer->renderPartial('_recurrings', ['data' => $paginator]);
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
                        InvRepository $invRepository
    )
    {
        $this->rbac($session);
        $parameters = [
            'title' => 'Add',
            'action' => ['recurring/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$settingRepository,
            'head'=>$head,
            
            'invs'=>$invRepository->findAllPreloaded(),
        ];
        
        if ($request->getMethod() === Method::POST) {
            
            $form = new RecurringForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->recurringService->saveRecurring(new Recurring(),$form);
                return $this->webService->getRedirectResponse('recurring/index');
            }
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function edit(ViewRenderer $head, SessionInterface $session, Request $request, 
                        ValidatorInterface $validator,
                        RecurringRepository $recurringRepository, 
                        SettingRepository $settingRepository,                        
                        InvRepository $invRepository
    ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => 'Edit',
            'action' => ['recurring/edit', ['id' => $this->recurring($request, $recurringRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->recurring($request, $recurringRepository)),
            'head'=>$head,
            's'=>$settingRepository,
                        'invs'=>$invRepository->findAllPreloaded()
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new RecurringForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->recurringService->saveRecurring($this->recurring($request,$recurringRepository), $form);
                return $this->webService->getRedirectResponse('recurring/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function delete(SessionInterface $session,Request $request,RecurringRepository $recurringRepository 
    ): Response {
        $this->rbac($session);
       
        $this->recurringService->deleteRecurring($this->recurring($request,$recurringRepository));               
        return $this->webService->getRedirectResponse('recurring/index');        
    }
    
    public function view(SessionInterface $session,Request $request,RecurringRepository $recurringRepository,
        SettingRepository $settingRepository,
        ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('view'),
            'action' => ['recurring/edit', ['id' => $this->recurring($request, $recurringRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->recurring($request, $recurringRepository)),
            's'=>$settingRepository,             
            'recurring'=>$recurringRepository->repoRecurringquery($this->recurring($request, $recurringRepository)->getId()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }
    
    private function rbac(SessionInterface $session) 
    {
        $canEdit = $this->userService->hasPermission('editRecurring');
        if (!$canEdit){
            $this->flash($session,'warning', 'You do not have the required permission.');
            return $this->webService->getRedirectResponse('recurring/index');
        }
        return $canEdit;
    }
    
    private function recurring(Request $request,RecurringRepository $recurringRepository) 
    {
        $id = $request->getAttribute('id');       
        $recurring = $recurringRepository->repoRecurringquery($id);
        if ($recurring === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $recurring;
    }
    
    private function recurrings(RecurringRepository $recurringRepository) 
    {
        $recurrings = $recurringRepository->findAllPreloaded();        
        if ($recurrings === null) {
            return $this->webService->getNotFoundResponse();
        };
        return $recurrings;
    }
    
    private function body($recurring) {
        $body = [
                
          'id'=>$recurring->getId(),
          'start_date'=>$recurring->getStart_date(),
          'end_date'=>$recurring->getEnd_date(),
          'frequency'=>$recurring->getFrequency(),
          'next_date'=>$recurring->getNext_date(),
          'inv_id'=>$recurring->getInv_id()
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