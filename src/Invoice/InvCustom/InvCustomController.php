<?php
declare(strict_types=1); 

namespace App\Invoice\InvCustom;

use App\Invoice\CustomField\CustomFieldRepository;
use App\Invoice\Entity\InvCustom;
use App\Invoice\Inv\InvRepository;
use App\Invoice\InvCustom\InvCustomService;
use App\Invoice\InvCustom\InvCustomRepository;
use App\Invoice\Setting\SettingRepository;

use App\User\UserService;
use App\Service\WebControllerService;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Yiisoft\Http\Method;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Session\Flash\Flash;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\View\ViewRenderer;
use \Exception;

final class InvCustomController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService;
    private TranslatorInterface $translator;
        
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UserService $userService,
        InvCustomService $invcustomService,
        TranslatorInterface $translator
    )    
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/invcustom')
                                           ->withLayout(dirname(dirname(__DIR__)).'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->userService = $userService;
        $this->invcustomService = $invcustomService;
        $this->translator = $translator;
    }
    
    public function index(SessionInterface $session, InvCustomRepository $invcustomRepository, SettingRepository $settingRepository): Response
    {      
         $canEdit = $this->rbac($session);
         $flash = $this->flash($session, '','');
         $parameters = [
          's'=>$settingRepository,
          'canEdit' => $canEdit,
          'invcustoms' => $this->invcustoms($invcustomRepository),
          'flash'=> $flash
         ];
        
        return $this->viewRenderer->render('index', $parameters);
    }
    
    public function index_adv_paginator(SessionInterface $session, InvCustomRepository $invcustomRepository, SettingRepository $settingRepository, Request $request, InvCustomService $service): Response
    {
                  
        $canEdit = $this->rbac($session);
        $flash = $this->flash($session, '','');
        $parameters = [
            's'=>$settingRepository,
            'canEdit' => $canEdit,
            'invcustoms' => $this->invcustoms($invcustomRepository),
            'flash'=> $flash
        ];
        return $this->viewRenderer->render('index', $parameters);  
    }
    
    public function add(ViewRenderer $head,SessionInterface $session, Request $request, 
                        ValidatorInterface $validator,
                        SettingRepository $settingRepository,                        
                        CustomFieldRepository $custom_fieldRepository,
                        InvRepository $invRepository
    )
    {
        $this->rbac($session);
        $parameters = [
            'title' => 'Add',
            'action' => ['invcustom/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$settingRepository,
            'head'=>$head,            
            'custom_fields'=>$custom_fieldRepository->findAllPreloaded(),
            'invs'=>$invRepository->findAllPreloaded(),
        ];
        
        if ($request->getMethod() === Method::POST) {
            
            $form = new InvCustomForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->invcustomService->saveInvCustom(new InvCustom(),$form);
                return $this->webService->getRedirectResponse('invcustom/index');
            }
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function edit(ViewRenderer $head, SessionInterface $session, Request $request, CurrentRoute $currentRoute,
                        ValidatorInterface $validator,
                        InvCustomRepository $invcustomRepository, 
                        SettingRepository $settingRepository,                        
                        CustomFieldRepository $custom_fieldRepository,
                        InvRepository $invRepository
    ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => 'Edit',
            'action' => ['invcustom/edit', ['id' => $this->invcustom($currentRoute, $invcustomRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->invcustom($currentRoute, $invcustomRepository)),
            'head'=>$head,
            's'=>$settingRepository,
            'custom_fields'=>$custom_fieldRepository->findAllPreloaded(),
            'invs'=>$invRepository->findAllPreloaded()
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new InvCustomForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->invcustomService->saveInvCustom($this->invcustom($currentRoute, $invcustomRepository), $form);
                return $this->webService->getRedirectResponse('invcustom/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function delete(SessionInterface $session, CurrentRoute $currentRoute, InvCustomRepository $invcustomRepository 
    ): Response {
        $this->rbac($session);
        try {
            $this->invcustomService->deleteInvCustom($this->invcustom($currentRoute, $invcustomRepository));               
            $this->flash($session, 'info', 'Deleted.');
            return $this->webService->getRedirectResponse('invcustom/index'); 
	} catch (Exception $e) {
            //unset($e);
            $this->flash($session, 'danger', $e);
            return $this->webService->getRedirectResponse('invcustom/index'); 
        }
    }
    
    public function view(SessionInterface $session, CurrentRoute $currentRoute, InvCustomRepository $invcustomRepository,
        SettingRepository $settingRepository,
        ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('view'),
            'action' => ['invcustom/view', ['id' => $this->invcustom($currentRoute, $invcustomRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->invcustom($currentRoute, $invcustomRepository)),
            's'=>$settingRepository,             
            'invcustom'=>$invcustomRepository->repoInvCustomquery($this->invcustom($currentRoute, $invcustomRepository)->getId()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }
        
    private function rbac(SessionInterface $session) 
    {
        $canEdit = $this->userService->hasPermission('editInvCustom');
        if (!$canEdit){
            $this->flash($session,'warning', $this->translator->translate('invoice.permission'));
            return $this->webService->getRedirectResponse('invcustom/index');
        }
        return $canEdit;
    }
    
    private function invcustom(CurrentRoute $currentRoute, InvCustomRepository $invcustomRepository) 
    {
        $id = $currentRoute->getArgument('id');       
        $invcustom = $invcustomRepository->repoInvCustomquery($id);
        if ($invcustom === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $invcustom;
    }
    
    private function invcustoms(InvCustomRepository $invcustomRepository) 
    {
        $invcustoms = $invcustomRepository->findAllPreloaded();        
        if ($invcustoms === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $invcustoms;
    }
    
    private function body($invcustom) {
        $body = [
                
          'id'=>$invcustom->getId(),
          'inv_id'=>$invcustom->getInv_id(),
          'custom_field_id'=>$invcustom->getCustom_field_id(),
          'value'=>$invcustom->getValue()
                ];
        return $body;
    }
    
    private function flash(SessionInterface $session, $level, $message){
        $flash = new Flash($session);
        $flash->set($level, $message); 
        return $flash;
    }
}