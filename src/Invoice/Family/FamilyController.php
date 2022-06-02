<?php

declare(strict_types=1);

namespace App\Invoice\Family;

use App\Invoice\Entity\Family;
use App\Invoice\Family\FamilyForm;
use App\Invoice\Family\FamilyRepository;
use App\Invoice\Setting\SettingRepository;
use App\Service\WebControllerService;
use App\User\UserService;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Yiisoft\Http\Method;
use Yiisoft\Json\Json;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Session\SessionInterface as Session;
use Yiisoft\Session\Flash\Flash;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\View\ViewRenderer;
use \Exception;

final class FamilyController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private FamilyService $familyService;    
    private UserService $userService;
    private TranslatorInterface $translator;

    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        FamilyService $familyService,
        UserService $userService,
        TranslatorInterface $translator
    ) {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/family')
                                           ->withLayout(dirname(dirname(__DIR__)).'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->familyService = $familyService;
        $this->userService = $userService;
        $this->translator = $translator;
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
            'flash'=>$flash
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
        try { 
                if ($request->getMethod() === Method::POST) {
                    $form = new FamilyForm();
                    if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                        $this->familyService->saveFamily(new Family(), $form);
                        return $this->webService->getRedirectResponse('family/index');  
                    } 
                    $parameters['errors'] = $form->getFormErrors();
                }
                return $this->viewRenderer->render('__form', $parameters);
        } catch (Exception $e) {
                unset($e);
                $this->flash($session, 'info', 'Fill in all the fields.');
                return $this->viewRenderer->render('__form', $parameters);
        }
        return $this->viewRenderer->render('__form', $parameters);        
    }

    public function edit(Session $session, CurrentRoute $currentRoute, Request $request, SettingRepository $settingRepository, FamilyRepository $familyRepository, ValidatorInterface $validator): Response 
    {
        $this->rbac($session);
        $family = $this->family($currentRoute, $familyRepository);
        $parameters = [
            'title' => 'Edit family',
            'action' => ['family/edit', ['id' => $family->getFamily_id()]],
            'errors' => [],
            'body' => [
                'family_name' => $this->family($currentRoute, $familyRepository)->getFamily_name(),
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
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('__form', $parameters);
    }
    
    public function delete(Session $session, CurrentRoute $currentRoute, FamilyRepository $familyRepository): Response 
    {
        $this->rbac($session);
        try {
            $family = $this->family($currentRoute, $familyRepository);
            $this->familyService->deleteFamily($family);               
            return $this->webService->getRedirectResponse('family/index');  
	} catch (Exception $e) {
            unset($e);
            $this->flash($session, 'danger', 'Cannot delete. Family history exists.');
            return $this->webService->getRedirectResponse('family/index');  
        }
    }
    
    public function test(Request $request, FamilyRepository $familyRepository, Session $session): Response  {
        $this->rbac($session);
        $queryparams = $request->getUri()->getQuery();
        //$queryparams = $request->getQueryParams();
        $id = $queryparams['id'];
        $title = $queryparams['title'];
        $family_name = $queryparams['family_name'];  
        $parameters = [
            'body' => [
                'title' => $title,
                'id'=>$id,
                'family_name'=>$family_name,
            ],
        ];
        
        if($this->isAjaxRequest($request)){
            //return $this->viewRenderer->renderPartial('__view',$parameters);
             return $this->webService->getRedirectResponse('family/index');             
        } else { 
            //return $this->viewRenderer->render('__view',$parameters);
            return $this->webService->getNotFoundResponse();
        }
    }
    
    public function test_old() {
        return Json::encode(['title'=>'Ross']);
    }
    
    private function isAjaxRequest(Request $request): bool
    {
        return $request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
    }
    
    public function view(Session $session, CurrentRoute $currentRoute, FamilyRepository $familyRepository,SettingRepository $settingRepository): Response {
        $this->rbac($session);        
        $family = $this->family($currentRoute, $familyRepository);
        $parameters = [
            'title' => 'View',
            'action' => ['family/view', ['id' => $family->getFamily_id()]],
            'errors' => [],
            'family'=>$this->family($currentRoute,$familyRepository),
            's'=>$settingRepository,     
            'body' => [
                'title' => 'View',
                'id'=>$family->getFamily_id(),
                'family_name'=>$family->getFamily_name(),
            ],            
        ];
        return $this->viewRenderer->render('__view', $parameters);
    }
    
    //$canEdit = $this->rbac();
    private function rbac(Session $session) {
        $canEdit = $this->userService->hasPermission('editFamily');
        if (!$canEdit){
            $this->flash($session,'warning', $this->translator->translate('invoice.permission'));
            return $this->webService->getRedirectResponse('family/index');
        }
        return $canEdit;
    }
    
    //$family = $this->family();
    private function family(CurrentRoute $currentRoute, FamilyRepository $familyRepository){
        $family_id = $currentRoute->getArgument('id');
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
