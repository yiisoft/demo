<?php

declare(strict_types=1);

namespace App\Invoice\Unit;

use App\Invoice\Entity\Unit;
use App\Invoice\Unit\UnitRepository;
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

final class UnitController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UnitService $unitService;    
    private UserService $userService;

    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UnitService $unitService,
        UserService $userService    
    ) {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/unit')
                                           ->withLayout(dirname(dirname(__DIR__)).'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->unitService = $unitService;
        $this->userService = $userService;
    }

    public function index(Session $session,UnitRepository $unitRepository, SettingRepository $settingRepository): Response
    {
        $canEdit = $this->rbac($session);
        $units = $this->units($unitRepository); 
        $flash = $this->flash($session, 'success', 'Help information will appear here.');
        $parameters = [
            's'=>$settingRepository,
            'canEdit' => $canEdit,
            'units' => $units, 
            'flash'=>$flash,
        ]; 
        return $this->viewRenderer->render('index', $parameters);
    }

    public function add(Session $session, Request $request, UnitRepository $unitRepository, SettingRepository $settingRepository, ValidatorInterface $validator): Response
    {
        $this->rbac($session);
        $parameters = [
            'title' => 'Add Unit',
            'action' => ['unit/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$settingRepository
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new UnitForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->unitService->saveUnit(new Unit(), $form);
                return $this->webService->getRedirectResponse('unit/index');
            }
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('__form', $parameters);
    }

    public function edit(Session $session, Request $request, UnitRepository $unitRepository, SettingRepository $settingRepository, ValidatorInterface $validator): Response 
    {
        $this->rbac($session);
        $unit = $this->unit($request, $unitRepository);
        $parameters = [
            'title' => 'Edit unit',
            'action' => ['unit/edit', ['unit_id' => $unit->id]],
            'errors' => [],
            'body' => [
                'unit_name' => $this->unit($request,$unitRepository)->getUnit_name(),
                'unit_name_plrl' => $this->unit($request,$unitRepository)->getUnit_name_plrl(),
            ],
            's'=>$settingRepository,
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new UnitForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->unitService->saveUnit($unit, $form);
                return $this->webService->getRedirectResponse('unit/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('__form', $parameters);
    }
    
    public function delete(Session $session, Request $request, UnitRepository $unitRepository): Response 
    {
        $this->rbac($session);
        try {
              $unit = $this->unit($request,$unitRepository);              
              $this->unitService->deleteUnit($unit);               
              return $this->webService->getRedirectResponse('unit/index');
	} catch (Exception $e) {
              unset($e);
              $this->flash($session, 'danger', 'Cannot delete. Unit history exists.');
              return $this->webService->getRedirectResponse('unit/index');
        }
    }
    
    public function view(Session $session,Request $request,UnitRepository $unitRepository,SettingRepository $settingRepository, ValidatorInterface $validator): Response {
        $this->rbac($session);        
        $unit = $this->unit($request, $unitRepository);
        $parameters = [
            'title' => $settingRepository->trans('edit_setting'),
            'action' => ['unit/edit', ['unit_id' => $unit->id]],
            'errors' => [],
            'unit'=>$this->unit($request,$unitRepository),
            's'=>$settingRepository,     
            'body' => [
                'unit_id'=>$unit->id,
                'unit_name'=>$unit->getUnit_name(),
                'unit_name_plrl'=>$unit->getUnit_name_plrl(),               
            ],            
        ];
        return $this->viewRenderer->render('__view', $parameters);
    }
    
    //$canEdit = $this->rbac();
    private function rbac(Session $session) {
        $canEdit = $this->userService->hasPermission('editUnit');
        if (!$canEdit){
            $this->flash($session,'warning', 'You do not have the required permission.');
            return $this->webService->getRedirectResponse('unit/index');
        }
        return $canEdit;
    }
    
    //$unit = $this->unit();
    private function unit(Request $request, UnitRepository $unitRepository){
        $unit_id = $request->getAttribute('unit_id');
        $unit = $unitRepository->repoUnitquery($unit_id);
        if ($unit === null) {
            return $this->webService->getNotFoundResponse();
        }        
        return $unit; 
    }
    
    //$units = $this->units();
    private function units(UnitRepository $unitRepository){
        $units = $unitRepository->findAllPreloaded();
        if ($units === null) {
            return $this->webService->getNotFoundResponse();
        };
        return $units;
    }
    
    //$this->flash
    private function flash(Session $session, $level, $message){
        $flash = new Flash($session);
        $flash->set($level, $message); 
        return $flash;
    }
}
