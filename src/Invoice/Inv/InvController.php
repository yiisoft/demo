<?php
declare(strict_types=1); 

namespace App\Invoice\Inv;

use App\Invoice\Entity\Inv;
use App\Invoice\Inv\InvService;
use App\Invoice\Inv\InvRepository;
use App\Invoice\Setting\SettingRepository;
use App\Invoice\Group\GroupRepository;
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
use Exception;

final class InvController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private InvService $invService;
    private UserService $userService;
    
    
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        InvService $invService,        
        UserService $userService
    )    
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/inv')
                                           ->withLayout(dirname(dirname(__DIR__)).'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->invService = $invService;        
        $this->userService = $userService;
    }
    
    public function index(SessionInterface $session, InvRepository $invRepository, SettingRepository $settingRepository, Request $request, InvService $service): Response
    {
         $canEdit = $this->rbac($session);
         $flash = $this->flash($session, 'success' , 'Change the type from success to info and you will get a flash message!.');
         $parameters = [      
          's'=>$settingRepository,
          'canEdit' => $canEdit,
          'invs' => $this->invs($invRepository),
          'flash'=> $flash
         ];
         
        return $this->viewRenderer->render('index', $parameters);
    }
    
    public function add(ViewRenderer $head,SessionInterface $session, Request $request, 
                        ValidatorInterface $validator,
                        SettingRepository $settingRepository, 
                        GroupRepository $groupRepository,
                        ClientRepository $clientRepository
    )
    {
        $this->rbac($session);
        $parameters = [
            'title' => 'Add',
            'action' => ['inv/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$settingRepository,
            'head'=>$head,
            'groups'=>$groupRepository->findAllPreloaded(),
            'clients'=>$clientRepository->findAllPreloaded(),
        ];
        
        if ($request->getMethod() === Method::POST) {
            
            $form = new InvForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->invService->saveInv($this->userService->getUser(),new Inv(),$form,$settingRepository, $groupRepository);
                return $this->webService->getRedirectResponse('inv/index');
            }
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function edit(ViewRenderer $head, SessionInterface $session, Request $request, 
                        ValidatorInterface $validator,
                        InvRepository $invRepository, 
                        SettingRepository $settingRepository, 
                        GroupRepository $groupRepository,
                        ClientRepository $clientRepository
    ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => 'Edit',
            'action' => ['inv/edit', ['id' => $this->inv($request, $invRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->inv($request, $invRepository)),
            's'=>$settingRepository,
            'head'=>$head,
            'groups'=>$groupRepository->findAllPreloaded(),
            'clients'=>$clientRepository->findAllPreloaded()
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new InvForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->invService->saveInv($this->userService->getUser(),$this->inv($request,$invRepository), $form, $settingRepository, $groupRepository);
                return $this->webService->getRedirectResponse('inv/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function delete(SessionInterface $session,Request $request,InvRepository $invRepository 
    ): Response {
        $this->rbac($session);       
        try {
            $this->invService->deleteInv($this->inv($request,$invRepository));               
            return $this->webService->getRedirectResponse('inv/index');        
	} catch (Exception $e) {
            unset($e);
            $this->flash($session, 'danger', 'Cannot delete. Invoice history exists.');
            return $this->webService->getRedirectResponse('inv/index');        
        }
    }
    
    public function view(SessionInterface $session,Request $request,InvRepository $invRepository,
        SettingRepository $settingRepository,
        ValidatorInterface $validator
        ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('view'),
            'action' => ['inv/edit', ['id' => $this->inv($request, $invRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->inv($request, $invRepository)),
            's'=>$settingRepository, 
            'inv'=>$invRepository->repoInvquery($this->inv($request, $invRepository)->getId()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }
    
    private function rbac(SessionInterface $session) 
    {
        $canEdit = $this->userService->hasPermission('editInv');
        if (!$canEdit){
            $this->flash($session,'warning', 'You do not have the required permission.');
            return $this->webService->getRedirectResponse('inv/index');
        }
        return $canEdit;
    }
    
    private function inv(Request $request,InvRepository $invRepository) 
    {
        $id = $request->getAttribute('id');       
        $inv = $invRepository->repoInvquery($id);
        if ($inv === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $inv;
    }
    
    private function invs(InvRepository $invRepository) 
    {
        $invs = $invRepository->findAllPreloaded();        
        if ($invs === null) {
            return $this->webService->getNotFoundResponse();
        };
        return $invs;
    }
    
    private function body($inv) {
        $body = [
          'id'=>$inv->getId(),
          'client_id'=>$inv->getClient_id(),
          'group_id'=>$inv->getGroup_id(),
          'status_id'=>$inv->getStatus_id(),
          'is_read_only'=>$inv->getIs_read_only(),
          'password'=>$inv->getPassword(),
          'date_created'=>$inv->getDate_created(),
          'time_created'=>$inv->getTime_created(),
          'date_modified'=>$inv->getDate_modified(),
          'date_due'=>$inv->getDate_due(),
          'number'=>$inv->getNumber(),
          'discount_amount'=>$inv->getDiscount_amount(),
          'discount_percent'=>$inv->getDiscount_percent(),
          'terms'=>$inv->getTerms(),
          'url_key'=>$inv->getUrl_key(),
          'payment_method'=>$inv->getPayment_method(),
          'creditinvoice_parent_id'=>$inv->getCreditinvoice_parent_id()
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