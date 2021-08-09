<?php

declare(strict_types=1); 

namespace App\Invoice\Task;

use App\Invoice\Entity\Task;
use App\Invoice\Task\TaskService;
use App\Invoice\Task\TaskRepository;
use App\Invoice\Setting\SettingRepository;
use App\Invoice\Project\ProjectRepository;
use App\Invoice\TaxRate\TaxRateRepository;
use App\User\UserService;
use Yiisoft\Validator\ValidatorInterface;
use App\Service\WebControllerService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Http\Method;
use Yiisoft\Yii\View\ViewRenderer;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Session\Flash\Flash;

final class TaskController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService;
    private TaskService $taskService;
    
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UserService $userService,
        TaskService $taskService
    )    
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice\task')
                                           ->withLayout(dirname(dirname(__DIR__)).'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->userService = $userService;
        $this->taskService = $taskService;
    }
    
    public function index(SessionInterface $session, TaskRepository $taskRepository, SettingRepository $settingRepository, Request $request, TaskService $service): Response
    {
       
         $canEdit = $this->rbac($session);
         $flash = $this->flash($session, 'success' , 'Change the type from success to info and you will get a flash message!.');
         $parameters = [      
          's'=>$settingRepository,
          'canEdit' => $canEdit,
          'tasks' => $this->tasks($taskRepository),
          'flash'=> $flash
         ];

        if ($this->isAjaxRequest($request)) {
            return $this->viewRenderer->renderPartial('_tasks', ['data' => $paginator]);
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
                        ProjectRepository $projectRepository,
                        TaxRateRepository $tax_rateRepository
    )
    {
        $this->rbac($session);
        $parameters = [
            'title' => 'Add',
            'action' => ['task/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$SettingRepository,
            'head'=>$head,            
            'projects'=>$projectRepository->findAllPreloaded(),
            'tax_rates'=>$tax_rateRepository->findAllPreloaded(),
        ];
        
        if ($request->getMethod() === Method::POST) {
            
            $form = new TaskForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->taskService->saveTask(new Task(),$form);
                return $this->webService->getRedirectResponse('task/index');
            }
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function edit(ViewRenderer $head, SessionInterface $session, Request $request, 
                        ValidatorInterface $validator,
                        TaskRepository $taskRepository, 
                        SettingRepository $settingRepository,                        
                        ProjectRepository $projectRepository,
                        TaxRateRepository $tax_rateRepository
    ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => 'Edit',
            'action' => ['task/edit', ['id' => $this->task($request, $taskRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->task($request, $taskRepository)),
            'head'=>$head,
            's'=>$settingRepository,
            'head'=>$head,
            'projects'=>$projectRepository->findAllPreloaded(),
            'tax_rates'=>$tax_rateRepository->findAllPreloaded()
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new TaskForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->taskService->saveTask($this->task($request,$taskRepository), $form);
                return $this->webService->getRedirectResponse('task/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function delete(SessionInterface $session,Request $request,TaskRepository $taskRepository 
    ): Response {
        $this->rbac($session);
       
        $this->taskService->deleteTask($this->task($request,$taskRepository));               
        return $this->webService->getRedirectResponse('task/index');        
    }
    
    public function view(SessionInterface $session,Request $request,TaskRepository $taskRepository,
        SettingRepository $settingRepository,
        ValidatorInterface $validator
        ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('view'),
            'action' => ['invoice/edit', ['id' => $this->task($request, $taskRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->task($request, $taskRepository)),
            's'=>$settingRepository,
            //load Entity\Product BelongTo relations ie. $family, $tax_rate, $unit by means of repoProductQuery             
            'task'=>$taskRepository->repoTaskquery($this->task($request, $taskRepository)->getId()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }
    
    private function rbac(SessionInterface $session) 
    {
        $canEdit = $this->userService->hasPermission('editTask');
        if (!$canEdit){
            $this->flash($session,'warning', 'You do not have the required permission.');
            return $this->webService->getRedirectResponse('task/index');
        }
        return $canEdit;
    }
    
    private function task(Request $request,TaskRepository $taskRepository) 
    {
        $id = $request->getAttribute('id');       
        $task = $taskRepository->repoTaskquery($id);
        if ($task === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $task;
    }
    
    private function tasks(TaskRepository $taskRepository) 
    {
        $tasks = $taskRepository->findAllPreloaded();        
        if ($tasks === null) {
            return $this->webService->getNotFoundResponse();
        };
        return $tasks;
    }
    
    private function body($task) {
        $body = [                
          'id'=>$task->getid(),
          'project_id'=>$task->getProject_id(),
          'task_name'=>$task->getTask_name(),
          'task_description'=>$task->getTask_description(),
          'task_price'=>$task->getTask_price(),
          'task_finish_date'=>$task->getTask_finish_date(),
          'task_status'=>$task->getTask_status(),
          'tax_rate_id'=>$task->getTax_rate_id()
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