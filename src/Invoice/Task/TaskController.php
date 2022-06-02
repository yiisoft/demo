<?php

declare(strict_types=1); 

namespace App\Invoice\Task;

use App\Invoice\Entity\Task;
use App\Invoice\Helpers\DateHelper;
use App\Invoice\Project\ProjectRepository;
use App\Invoice\Setting\SettingRepository;
use App\Invoice\Task\TaskService;
use App\Invoice\Task\TaskRepository;
use App\Invoice\TaxRate\TaxRateRepository;
use App\Service\WebControllerService;
use App\User\UserService;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Http\Method;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Session\Flash\Flash;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Yii\View\ViewRenderer;
use Yiisoft\Validator\ValidatorInterface;
use \Exception;

final class TaskController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService;
    private TaskService $taskService;
    private TranslatorInterface $translator;
    
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UserService $userService,
        TaskService $taskService,
        TranslatorInterface $translator
    )    
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/task')
                                           ->withLayout(dirname(dirname(__DIR__)).'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->userService = $userService;
        $this->taskService = $taskService;
        $this->translator = $translator;
    }
    
    public function index(SessionInterface $session, TaskRepository $taskRepository, DateHelper $dateHelper, SettingRepository $settingRepository, Request $request, TaskService $service): Response
    {            
        $pageNum = (int)$request->getAttribute('page','1');
        $paginator = (new OffsetPaginator($this->tasks($taskRepository)))
         ->withPageSize((int)$settingRepository->setting('default_list_limit'))
        ->withCurrentPage($pageNum);      
        $canEdit = $this->rbac($session);
        $flash = $this->flash($session, '','');
        $parameters = [
                'paginator' => $paginator,
                'd'=>$dateHelper,
                's'=>$settingRepository,
                'canEdit' => $canEdit,
                'tasks' => $this->tasks($taskRepository),
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
            's'=>$settingRepository,
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
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function edit(ViewRenderer $head, SessionInterface $session, Request $request, CurrentRoute $currentRoute,
                        ValidatorInterface $validator,
                        TaskRepository $taskRepository, 
                        SettingRepository $settingRepository,                        
                        ProjectRepository $projectRepository,
                        TaxRateRepository $tax_rateRepository
    ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => 'Edit',
            'action' => ['task/edit', ['id' => $this->task($currentRoute, $taskRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->task($currentRoute, $taskRepository)),
            'head'=>$head,
            's'=>$settingRepository,
            'projects'=>$projectRepository->findAllPreloaded(),
            'tax_rates'=>$tax_rateRepository->findAllPreloaded()
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new TaskForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->taskService->saveTask($this->task($currentRoute, $taskRepository), $form);
                return $this->webService->getRedirectResponse('task/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function delete(SessionInterface $session, CurrentRoute $currentRoute, TaskRepository $taskRepository 
    ): Response {
        $this->rbac($session);
        try {
            $this->taskService->deleteTask($this->task($currentRoute, $taskRepository)); 
            $this->flash($session, 'info', 'Deleted.');
            return $this->webService->getRedirectResponse('task/index'); 
	} catch (Exception $e) {
            //unset($e);
            $this->flash($session, 'danger', $e);
            return $this->webService->getRedirectResponse('task/index'); 
        }
    }
    
    public function view(SessionInterface $session,CurrentRoute $currentRoute, TaskRepository $taskRepository,
        SettingRepository $settingRepository,
        ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('view'),
            'action' => ['task/view', ['id' => $this->task($currentRoute, $taskRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->task($currentRoute, $taskRepository)),
            's'=>$settingRepository,             
            'task'=>$taskRepository->repoTaskquery($this->task($currentRoute, $taskRepository)->getId()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }
        
    private function rbac(SessionInterface $session) 
    {
        $canEdit = $this->userService->hasPermission('editTask');
        if (!$canEdit){
            $this->flash($session,'warning', $this->translator->translate('invoice.permission'));
            return $this->webService->getRedirectResponse('task/index');
        }
        return $canEdit;
    }
    
    private function task(CurrentRoute $currentRoute, TaskRepository $taskRepository) 
    {
        $id = $currentRoute->getArgument('id');       
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
        }
        return $tasks;
    }
    
    private function body($task) {
        $body = [                
          'id'=>$task->getId(),
          'project_id'=>$task->getProject_id(),
          'name'=>$task->getName(),
          'description'=>$task->getDescription(),
          'price'=>$task->getPrice(),
          'finish_date'=>$task->getFinish_date(),
          'status'=>$task->getStatus(),
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