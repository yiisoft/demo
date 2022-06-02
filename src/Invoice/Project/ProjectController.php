<?php

declare(strict_types=1); 

namespace App\Invoice\Project;


use App\Invoice\Client\ClientRepository;
use App\Invoice\Entity\Project;
use App\Invoice\Project\ProjectService;
use App\Invoice\Project\ProjectRepository;
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
use \Exception;

final class ProjectController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService;
    private ProjectService $projectService;
    private TranslatorInterface $translator;
    
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UserService $userService,
        ProjectService $projectService,
        TranslatorInterface $translator,
    )    
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/project')
                                           ->withLayout(dirname(dirname(__DIR__)).'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->userService = $userService;
        $this->projectService = $projectService;
        $this->translator = $translator;
    }
    
    public function index(SessionInterface $session, ProjectRepository $projectRepository, SettingRepository $settingRepository, Request $request, ProjectService $service): Response
    {            
        $pageNum = (int)$request->getAttribute('page', '1');
        $paginator = (new OffsetPaginator($this->projects($projectRepository)))
        ->withPageSize((int)$settingRepository->setting('default_list_limit'))
        ->withCurrentPage($pageNum);      
        $canEdit = $this->rbac($session);
        $flash = $this->flash($session, '','');
        $parameters = [
              'paginator' => $paginator,  
              's'=>$settingRepository,
              'canEdit' => $canEdit,
              'projects' => $this->projects($projectRepository),
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
                        SettingRepository $settingRepository,                        
                        ClientRepository $clientRepository
    )
    {
        $this->rbac($session);
        $parameters = [
            'title' => 'Add',
            'action' => ['project/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$settingRepository,
            'head'=>$head,
            
            'clients'=>$clientRepository->findAllPreloaded(),
        ];
        
        if ($request->getMethod() === Method::POST) {
            
            $form = new ProjectForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->projectService->saveProject(new Project(),$form);
                return $this->webService->getRedirectResponse('project/index');
            }
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function edit(ViewRenderer $head, SessionInterface $session, Request $request, CurrentRoute $currentRoute,
                        ValidatorInterface $validator,
                        ProjectRepository $projectRepository, 
                        SettingRepository $settingRepository,                        
                        ClientRepository $clientRepository
    ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => 'Edit',
            'action' => ['project/edit', ['id' => $this->project($currentRoute, $projectRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->project($currentRoute, $projectRepository)),
            'head'=>$head,
            's'=>$settingRepository,
            'clients'=>$clientRepository->findAllPreloaded()
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new ProjectForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->projectService->saveProject($this->project($currentRoute, $projectRepository), $form);
                return $this->webService->getRedirectResponse('project/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function delete(SessionInterface $session, CurrentRoute $currentRoute, ProjectRepository $projectRepository 
    ): Response {
        $this->rbac($session);
        try {
            $this->projectService->deleteProject($this->project($currentRoute, $projectRepository));               
            return $this->webService->getRedirectResponse('project/index'); 
	} catch (Exception $e) {
            //unset($e);
            $this->flash($session, 'danger', $e);
            return $this->webService->getRedirectResponse('project/index'); 
        }
    }
    
    public function view(SessionInterface $session, CurrentRoute $currentRoute, ProjectRepository $projectRepository,
        SettingRepository $settingRepository,
        ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('view'),
            'action' => ['project/view', ['id' => $this->project($currentRoute, $projectRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->project($currentRoute, $projectRepository)),
            's'=>$settingRepository,             
            'project'=>$projectRepository->repoProjectquery($this->project($currentRoute, $projectRepository)->getId()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }
        
    private function rbac(SessionInterface $session) 
    {
        $canEdit = $this->userService->hasPermission('editProject');
        if (!$canEdit){
            $this->flash($session,'warning', $this->translator->translate('invoice.permission'));
            return $this->webService->getRedirectResponse('project/index');
        }
        return $canEdit;
    }
    
    private function project(CurrentRoute $currentRoute, ProjectRepository $projectRepository) 
    {
        $id = $currentRoute->getArgument('id');       
        $project = $projectRepository->repoProjectquery($id);
        if ($project === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $project;
    }
    
    private function projects(ProjectRepository $projectRepository) 
    {
        $projects = $projectRepository->findAllPreloaded();        
        if ($projects === null) {
            return $this->webService->getNotFoundResponse();
        };
        return $projects;
    }
    
    private function body($project) {
        $body = [                
          'id'=>$project->getId(),
          'client_id'=>$project->getClient_id(),
          'name'=>$project->getName()
                ];
        return $body;
    }
    
    private function flash(SessionInterface $session, $level, $message){
        $flash = new Flash($session);
        $flash->set($level, $message); 
        return $flash;
    }
}