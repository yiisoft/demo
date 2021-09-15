<?php

declare(strict_types=1);

namespace App\Invoice\Family;

use App\Invoice\Entity\Family;
use App\Invoice\Family\FamilyRepository;
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

final class FamilyController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private FamilyService $familyService;    
    private UserService $userService;

    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        FamilyService $familyService,
        UserService $userService    
    ) {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/family')
                                           ->withLayout(dirname(dirname(__DIR__)).'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->familyService = $familyService;
        $this->userService = $userService;
    }

    public function index(Session $session,FamilyRepository $familyRepository, SettingRepository $settingRepository): Response
    {
        $canEdit = $this->rbac($session);
        $familys = $this->familys($familyRepository);
        $flash = $this->flash($session, 'success', 'Help information will appear here.');
        $parameters = [
            's'=>$settingRepository,
            'canEdit' => $canEdit,
            'familys' => $familys, 
            'flash'=>$flash,
        ]; 
        return $this->viewRenderer->render('index', $parameters);
    }

    public function add(Session $session, Request $request,SettingRepository $settingRepository,ValidatorInterface $validator): Response
    {
        $this->rbac($session);
        $parameters = [
            'title' => 'Add Family',
            'action' => ['family/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$settingRepository
        ];
        
        if ($request->getMethod() === Method::POST) {
            $form = new FamilyForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->familyService->saveFamily(new Family(), $form);
                return $this->webService->getRedirectResponse('family/index');
            }
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('__form', $parameters);
    }

    public function edit(Session $session, Request $request, SettingRepository $settingRepository, FamilyRepository $familyRepository, ValidatorInterface $validator): Response 
    {
        $this->rbac($session);
        $family = $this->family($request, $familyRepository);
        $parameters = [
            'title' => 'Edit family',
            'action' => ['family/edit', ['family_id' => $family->getFamily_id()]],
            'errors' => [],
            'body' => [
                'family_name' => $this->family($request,$familyRepository)->getFamily_name(),
            ],
            's'=>$settingRepository,
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new FamilyForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->familyService->saveFamily($family, $form);
                return $this->webService->getRedirectResponse('family/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('__form', $parameters);
    }
    
    public function delete(Session $session, Request $request, FamilyRepository $familyRepository): Response 
    {
        $this->rbac($session);
        try {
            $family = $this->family($request,$familyRepository);
            $this->familyService->deleteFamily($family);               
            return $this->webService->getRedirectResponse('family/index');  
	} catch (Exception $e) {
            unset($e);
            $this->flash($session, 'danger', 'Cannot delete. Family history exists.');
            return $this->webService->getRedirectResponse('family/index');  
        }
    }
    
    public function view(Session $session,Request $request,FamilyRepository $familyRepository,SettingRepository $settingRepository,ValidatorInterface $validator): Response {
        $this->rbac($session);        
        $family = $this->family($request, $familyRepository);
        $parameters = [
            'title' => $settingRepository->trans('edit_family'),
            'action' => ['family/view', ['family_id' => $family->getFamily_id()]],
            'errors' => [],
            'family'=>$this->family($request,$familyRepository),
            's'=>$settingRepository,     
            'body' => [
                'family_id'=>$family->getFamily_id(),
                'family_name'=>$family->getFamily_name()               
            ],            
        ];
        return $this->viewRenderer->render('__view', $parameters);
    }
    
    //$canEdit = $this->rbac();
    private function rbac(Session $session) {
        $canEdit = $this->userService->hasPermission('editFamily');
        if (!$canEdit){
            $this->flash($session,'warning', 'You do not have the required permission.');
            return $this->webService->getRedirectResponse('family/index');
        }
        return $canEdit;
    }
    
    //$family = $this->family();
    private function family(Request $request, FamilyRepository $familyRepository){
        $family_id = $request->getAttribute('family_id');
        $family = $familyRepository->repoFamilyquery($family_id);
        if ($family === null) {
            return $this->webService->getNotFoundResponse();
        }        
        return $family; 
    }
    
    //$familys = $this->familys();
    private function familys(FamilyRepository $familyRepository){
        $familys = $familyRepository->findAllPreloaded();
        if ($familys === null) {
            return $this->webService->getNotFoundResponse();
        };
        return $familys;
    }
    
    //$this->flash
    private function flash(Session $session, $level, $message){
        $flash = new Flash($session);
        $flash->set($level, $message); 
        return $flash;
    }
}
