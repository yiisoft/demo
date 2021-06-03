<?php

declare(strict_types=1);

namespace App\Invoice\Setting;

use App\Invoice\Entity\Setting;
use App\Invoice\Setting\SettingRepository;
use App\Service\WebControllerService;
use App\User\UserService;
use Yiisoft\Http\Method;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\View\ViewRenderer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Session\SessionInterface as Session;
use Yiisoft\Session\Flash\Flash;

final class SettingController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private SettingService $settingService;    
    private UserService $userService;

    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        SettingService $settingService,
        UserService $userService    
    ) {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/setting');
        $this->webService = $webService;
        $this->settingService = $settingService;
        $this->userService = $userService;
    }

    public function index(Session $session,SettingRepository $settingRepository): Response
    {
        $canEdit = $this->rbac($session);
        $settings = $this->settings($settingRepository);
        $flash = $this->flash($session,'info', 'Clicking on the delete button will delete the record immediately so proceed with caution.');
        $parameters = [
            's'=>$settingRepository,
            'canEdit' => $canEdit,
            'settings' => $settings, 
            'flash'=>$flash,
        ]; 
        return $this->viewRenderer->render('index', $parameters);
    }

    public function add(Session $session, Request $request, SettingRepository $settingRepository,ValidatorInterface $validator): Response
    {
        $this->rbac($session);
        $parameters = [
            'title' => 'Add Setting',
            'action' => ['setting/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$settingRepository
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new SettingForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->settingService->saveSetting(new Setting(), $form);
                return $this->webService->getRedirectResponse('setting/index');
            }
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('__form', $parameters);
    }

    public function edit(Session $session, Request $request, SettingRepository $settingRepository, ValidatorInterface $validator): Response 
    {
        $this->rbac($session);
        $setting = $this->setting($request, $settingRepository);
        $parameters = [
            'title' => 'Edit setting',
            'action' => ['setting/edit', ['setting_id' => $setting->setting_id]],
            'errors' => [],
            'body' => [
                'setting_key' => $this->setting($request,$settingRepository)->getSetting_key(),
                'setting_value' => $this->setting($request,$settingRepository)->getSetting_value(),
            ],
            's'=>$settingRepository,
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new SettingForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->settingService->saveSetting($setting, $form);
                return $this->webService->getRedirectResponse('setting/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('__form', $parameters);
    }
    
    public function delete(Session $session, Request $request, SettingRepository $settingRepository): Response 
    {
        $this->rbac($session);
        $setting = $this->setting($request,$settingRepository);
        $this->flash($session,'danger','This record has been deleleted.');
        $this->settingService->deleteSetting($setting);               
        return $this->webService->getRedirectResponse('setting/index');        
    }
    
    public function view(Session $session,Request $request,SettingRepository $settingRepository,ValidatorInterface $validator): Response {
        $this->rbac($session);        
        $setting = $this->setting($request, $settingRepository);
        $parameters = [
            'title' => $settingRepository->trans('edit_setting'),
            'action' => ['setting/edit', ['setting_id' => $setting->setting_id]],
            'errors' => [],
            'setting'=>$this->setting($request,$settingRepository),
            's'=>$settingRepository,     
            'body' => [
                'setting_id'=>$setting->setting_id,
                'setting_key'=>$setting->getSetting_key(),
                'setting_value'=>$setting->getSetting_value(),               
            ],            
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new SettingForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->settingService->saveSetting($this->userService->getUser(),$setting, $form);
                return $this->webService->getRedirectResponse('setting/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('__view', $parameters);
    }
    
    //$canEdit = $this->rbac();
    private function rbac(Session $session) {
        $canEdit = $this->userService->hasPermission('editSetting');
        if (!$canEdit){
            $this->flash($session,'warning', 'You do not have the required permission.');
            return $this->webService->getRedirectResponse('setting/index');
        }
        return $canEdit;
    }
    
    //$setting = $this->setting();
    private function setting(Request $request, SettingRepository $settingRepository){
        $setting_id = $request->getAttribute('setting_id');
        $setting = $settingRepository->repoSettingquery($setting_id);
        if ($setting === null) {
            return $this->webService->getNotFoundResponse();
        }        
        return $setting; 
    }
    
    //$settings = $this->settings();
    private function settings(SettingRepository $settingRepository){
        $settings = $settingRepository->findAllPreloaded();
        if ($settings === null) {
            return $this->webService->getNotFoundResponse();
        };
        return $settings;
    }
    
    //$this->flash
    private function flash(Session $session, $level, $message){
        $flash = new Flash($session);
        $flash->set($level, $message); 
        return $flash;
    }
}
