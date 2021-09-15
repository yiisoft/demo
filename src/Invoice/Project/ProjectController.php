<?php

declare(strict_types=1); 

namespace App\Invoice\Project;

use App\Invoice\Entity\Project;
use App\Invoice\Project\ProjectService;
use App\Invoice\Project\ProjectRepository;
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

final class ProjectController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService;
    private ProjectService $projectService;
    
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UserService $userService,
        ProjectService $projectService
    )    
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/project')
                                           ->withLayout(dirname(dirname(__DIR__)).'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->userService = $userService;
        $this->projectService = $projectService;
    }
    
    public function index(SessionInterface $session, ProjectRepository $projectRepository, SettingRepository $settingRepository, Request $request, ProjectService $service): Response
    {
       
         $canEdit = $this->rbac($session);
         $flash = $this->flash($session, 'success' , 'Change the type from success to info and you will get a flash message!.');
         $parameters = [      
          's'=>$settingRepository,
          'canEdit' => $canEdit,
          'projects' => $this->projects($projectRepository),
          'flash'=> $flash
         ];

        if ($this->isAjaxRequest($request)) {
            return $this->viewRenderer->renderPartial('_projects', ['data' => $paginator]);
        }
        
        return $this->viewRenderer->render('index', $parameters);
    }
    
    private function isAjaxRequest(Request $request): bool
    {
        return $request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
    }
    
    public function add(ViewRenderer $head,SessionInterface $session, Request $request, 
                        ValidatorInterface $validator,
                        SettingRepository $SettingRepository,                        
                        ClientRepository $clientRepository
    )
    {
        $this->rbac($session);
        $parameters = [
            'title' => 'Add',
            'action' => ['project/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$SettingRepository,
            'head'=>$head,            
            'clients'=>$clientRepository->findAllPreloaded(),
        ];
        
        if ($request->getMethod() === Method::POST) {
            
            $form = new ProjectForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->projectService->saveProject(new Project(),$form);
                return $this->webService->getRedirectResponse('project/index');
            }
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function edit(ViewRenderer $head, SessionInterface $session, Request $request, 
                        ValidatorInterface $validator,
                        ProjectRepository $projectRepository, 
                        SettingRepository $settingRepository,                        
                        ClientRepository $clientRepository
    ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => 'Edit',
            'action' => ['project/edit', ['id' => $this->project($request, $projectRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->project($request, $projectRepository)),
            'head'=>$head,
            's'=>$settingRepository,
            'head'=>$head,
            'clients'=>$clientRepository->findAllPreloaded()
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new ProjectForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->projectService->saveProject($this->project($request,$projectRepository), $form);
                return $this->webService->getRedirectResponse('project/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function delete(SessionInterface $session,Request $request,ProjectRepository $projectRepository 
    ): Response {
        $this->rbac($session);
       
        $this->projectService->deleteProject($this->project($request,$projectRepository));               
        return $this->webService->getRedirectResponse('project/index');        
    }
    
    public function view(SessionInterface $session,Request $request,ProjectRepository $projectRepository,
        SettingRepository $settingRepository,
        ValidatorInterface $validator
        ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('view'),
            'action' => ['invoice/edit', ['id' => $this->project($request, $projectRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->project($request, $projectRepository)),
            's'=>$settingRepository,      
            'project'=>$projectRepository->repoProjectquery($this->project($request, $projectRepository)->getId()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }
    
    private function rbac(SessionInterface $session) 
    {
        $canEdit = $this->userService->hasPermission('editProject');
        if (!$canEdit){
            $this->flash($session,'warning', 'You do not have the required permission.');
            return $this->webService->getRedirectResponse('project/index');
        }
        return $canEdit;
    }
    
    private function project(Request $request,ProjectRepository $projectRepository) 
    {
        $id = $request->getAttribute('id');       
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
          'project_name'=>$project->getProject_name()
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