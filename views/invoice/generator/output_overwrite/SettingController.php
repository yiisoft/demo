<?php

declare(strict_types=1); 

namespace App\Invoice\Setting;

use App\Invoice\Entity\Setting;
use App\Invoice\Setting\SettingService;
use App\Invoice\Setting\SettingRepository;
use \Exception;
use App\Invoice\Setting\SettingRepository;
use App\User\UserService;
use Yiisoft\Validator\ValidatorInterface;
use App\Service\WebControllerService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Http\Method;
use Yiisoft\Yii\View\ViewRenderer;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Session\Flash\Flash;
use Yiisoft\Data\Paginator\OffsetPaginator;
final class SettingController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService;
    private SettingService $settingService;
    private const SETTINGS_PER_PAGE = 1;
    
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UserService $userService,
        SettingService $settingService
    )    
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/setting')
                                           ->withLayout(dirname(dirname(__DIR__)).'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->userService = $userService;
        $this->settingService = $settingService;
    }
    
    public function index_old(SessionInterface $session, SettingRepository $settingRepository, SettingRepository $settingRepository, Request $request, SettingService $service): Response
    {      
         $canEdit = $this->rbac($session);
         $flash = $this->flash($session, 'dummy' , 'Flash message!.');
         $parameters = [
      
          's'=>$settingRepository,
          'canEdit' => $canEdit,
          'settings' => $this->settings($settingRepository),
          'flash'=> $flash
         ];

        if ($this->isAjaxRequest($request)) {
            return $this->viewRenderer->renderPartial('_settings', ['data' => $paginator]);
        }
        
        return $this->viewRenderer->render('index_old', $parameters);
    }
    
    public function index_adv_paginator(SessionInterface $session, SettingRepository $settingRepository, SettingRepository $settingRepository, Request $request, SettingService $service): Response
    {
            
        $pageNum = (int)$request->getAttribute('page', 1);
        $paginator = (new OffsetPaginator($this->settings($settingRepository)))
        ->withPageSize(self::SETTINGS_PER_PAGE)
        ->withCurrentPage($pageNum);
      
        $canEdit = $this->rbac($session);
        $flash = $this->flash($session, 'dummy' , 'Flash message!.');
        $parameters = [
              'paginator' => $paginator,
  
              's'=>$settingRepository,
              'canEdit' => $canEdit,
        'settings' => $this->settings($settingRepository),
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

    )
    {
        $this->rbac($session);
        $parameters = [
            'title' => 'Add',
            'action' => ['setting/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$settingRepository,
            'head'=>$head,
            
        ];
        
        if ($request->getMethod() === Method::POST) {
            
            $form = new SettingForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->settingService->saveSetting(new Setting(),$form);
                return $this->webService->getRedirectResponse('setting/index');
            }
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function edit(ViewRenderer $head, SessionInterface $session, Request $request, 
                        ValidatorInterface $validator,
                        SettingRepository $settingRepository, 
                        SettingRepository $settingRepository,                        

    ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => 'Edit',
            'action' => ['setting/edit', ['id' => $this->setting($request, $settingRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->setting($request, $settingRepository)),
            'head'=>$head,
            's'=>$settingRepository,
            
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new SettingForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->settingService->saveSetting($this->setting($request,$settingRepository), $form);
                return $this->webService->getRedirectResponse('setting/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function delete(SessionInterface $session,Request $request,SettingRepository $settingRepository 
    ): Response {
        $this->rbac($session);
        try {
            $this->settingService->deleteSetting($this->setting($request,$settingRepository));               
            return $this->webService->getRedirectResponse('setting/index'); 
	} catch (Exception $e) {
            //unset($e);
            $this->flash($session, 'danger', $e);
            return $this->webService->getRedirectResponse('setting/index'); 
        }
    }
    
    public function view(SessionInterface $session,Request $request,SettingRepository $settingRepository,
        SettingRepository $settingRepository,
        ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('view'),
            'action' => ['setting/view', ['id' => $this->setting($request, $settingRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->setting($request, $settingRepository)),
            's'=>$settingRepository,             
            'setting'=>$settingRepository->repoSettingquery($this->setting($request, $settingRepository)->getId()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }
        
    private function rbac(SessionInterface $session) 
    {
        $canEdit = $this->userService->hasPermission('editSetting');
        if (!$canEdit){
            $this->flash($session,'warning', 'You do not have the required permission.');
            return $this->webService->getRedirectResponse('setting/index');
        }
        return $canEdit;
    }
    
    private function setting(Request $request,SettingRepository $settingRepository) 
    {
        $id = $request->getAttribute('id');       
        $setting = $settingRepository->repoSettingquery($id);
        if ($setting === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $setting;
    }
    
    private function settings(SettingRepository $settingRepository) 
    {
        $settings = $settingRepository->findAllPreloaded();        
        if ($settings === null) {
            return $this->webService->getNotFoundResponse();
        };
        return $settings;
    }
    
    private function body($setting) {
        $body = [
                
          'id'=>$setting->getId(),
          'setting_key'=>$setting->getSetting_key(),
          'setting_value'=>$setting->getSetting_value()
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