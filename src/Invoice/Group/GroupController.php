<?php

declare(strict_types=1); 

namespace App\Invoice\Group;

use App\Invoice\Entity\Group;
use App\Invoice\Group\GroupService;
use App\Invoice\Group\GroupRepository;
use App\Invoice\Setting\SettingRepository;
use App\Service\WebControllerService;
use App\User\UserService;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Http\Method;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Session\Flash\Flash;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\View\ViewRenderer;

final class GroupController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService;
    private GroupService $groupService;
    private TranslatorInterface $translator;
    
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UserService $userService,
        GroupService $groupService,
        TranslatorInterface $translator
    )    
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/group')
                                           ->withLayout(dirname(dirname(__DIR__) ).'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->userService = $userService;
        $this->groupService = $groupService;
        $this->translator = $translator;
    }
    
    public function index(SessionInterface $session, GroupRepository $groupRepository, SettingRepository $settingRepository, Request $request, GroupService $service): Response
    {    
        $pageNum = (int)$request->getAttribute('page', '1');
        $paginator = (new OffsetPaginator($this->groups($groupRepository)))
        ->withPageSize((int)$settingRepository->setting('default_list_limit'))
        ->withCurrentPage($pageNum);
        $canEdit = $this->rbac($session);
        $flash = $this->flash($session, '','');
        $parameters = [
              'paginator' => $paginator,
              's'=>$settingRepository,
              'canEdit' => $canEdit,
              'groups' => $this->groups($groupRepository),
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
                        SettingRepository $SettingRepository                        

    )
    {
        $this->rbac($session);
        $parameters = [
            'title' => 'Add',
            'action' => ['group/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$SettingRepository,
            'head'=>$head
        ];
        
        if ($request->getMethod() === Method::POST) {
            
            $form = new GroupForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->groupService->saveGroup(new Group(),$form);
                return $this->webService->getRedirectResponse('group/index');
            }
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function edit(ViewRenderer $head, SessionInterface $session, Request $request, CurrentRoute $currentRoute,
                        ValidatorInterface $validator,
                        GroupRepository $groupRepository, 
                        SettingRepository $settingRepository                       

    ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => 'Edit',
            'action' => ['group/edit', ['id' => $this->group($currentRoute, $groupRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->group($currentRoute, $groupRepository)),
            'head'=>$head,
            's'=>$settingRepository,
            'head'=>$head
            
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new GroupForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->groupService->saveGroup($this->group($currentRoute,$groupRepository), $form);
                return $this->webService->getRedirectResponse('group/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function delete(SessionInterface $session,CurrentRoute $currentRoute, GroupRepository $groupRepository 
    ): Response {
        $this->rbac($session);
        try {
              $this->groupService->deleteGroup($this->group($currentRoute, $groupRepository));               
              return $this->webService->getRedirectResponse('group/index'); 
	} catch (Exception $e) {
              unset($e);
              $this->flash($session, 'danger', 'Cannot delete. Group history exists.');
              return $this->webService->getRedirectResponse('group/index'); 
        }
    }
    
    public function view(SessionInterface $session, CurrentRoute $currentRoute, GroupRepository $groupRepository,
        SettingRepository $settingRepository
        ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('view'),
            'action' => ['invoice/edit', ['id' => $this->group($currentRoute, $groupRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->group($currentRoute, $groupRepository)),
            's'=>$settingRepository,            
            'group'=>$groupRepository->repoGroupquery($this->group($currentRoute, $groupRepository)->getId()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }
    
    private function rbac(SessionInterface $session) 
    {
        $canEdit = $this->userService->hasPermission('editGroup');
        if (!$canEdit){
            $this->flash($session,'warning', $this->translator->translate('invoice.permission'));
            return $this->webService->getRedirectResponse('group/index');
        }
        return $canEdit;
    }
    
    private function group(CurrentRoute $currentRoute, GroupRepository $groupRepository) 
    {
        $id = $currentRoute->getArgument('id');       
        $group = $groupRepository->repoGroupquery($id);
        if ($group === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $group;
    }
    
    private function groups(GroupRepository $groupRepository) 
    {
        $groups = $groupRepository->findAllPreloaded();        
        if ($groups === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $groups;
    }
    
    private function body($group) {
        $body = [
                
          'id'=>$group->getId(),
          'name'=>$group->getName(),
          'identifier_format'=>$group->getIdentifier_format(),
          'next_id'=>$group->getNext_id(),
          'left_pad'=>$group->getLeft_pad()
                ];
        return $body;
    }
    
    private function flash(SessionInterface $session, $level, $message){
        $flash = new Flash($session);
        $flash->set($level, $message); 
        return $flash;
    }
}