<?php

declare(strict_types=1);

namespace App\Invoice\Setting;

use App\Invoice\Entity\Setting;
use App\Service\WebControllerService;
use App\User\UserService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Http\Method;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\View\ViewRenderer;

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

    public function index(Request $request, SettingRepository $settingRepository): Response
    {
        $canEdit = $this->userService->hasPermission('editSetting');
        $setting_id = $request->getAttribute('setting_id', null);
        $item = $settingRepository->fullSettingPage($setting_id);
        if ($item === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $this->viewRenderer->render('index', ['item' => $item, 'canEdit' => $canEdit,'setting_id'=>$setting_id]);
    }

    public function add(Request $request, ValidatorInterface $validator): Response
    {
        $parameters = [
            'title' => 'Add Setting',
            'action' => ['setting/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
        ];

        if ($request->getMethod() === Method::POST) {
            $form = new SettingForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->settingService->saveSetting(new Setting(), $form);
                return $this->webService->getRedirectResponse('invoice/index');
            }

            $parameters['errors'] = $form->getFirstErrors();
        }

        return $this->viewRenderer->render('__form', $parameters);
    }

    public function edit(
        Request $request,
        SettingRepository $settingRepository,
        ValidatorInterface $validator
    ): Response {
        $setting_id = $request->getAttribute('setting_id', null);
        $setting = $settingRepository->fullSettingPage($setting_id);
        if ($setting === null) {
            return $this->webService->getNotFoundResponse();
        }
        
        $parameters = [
            'title' => 'Edit setting',
            'action' => ['setting/edit', ['setting_id' => $setting_id]],
            'errors' => [],
            'body' => [
                'setting_key' => $setting->getSetting_key(),
                'setting_value' => $setting->getSetting_value(),
            ],
        ];

        if ($request->getMethod() === Method::POST) {
            $form = new SettingForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->settingService->saveSetting($setting, $form);
                return $this->webService->getRedirectResponse('invoice/index');
            }

            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFirstErrors();
        }

        return $this->viewRenderer->render('__form', $parameters);
    }
}
