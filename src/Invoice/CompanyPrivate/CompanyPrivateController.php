<?php

declare(strict_types=1); 

namespace App\Invoice\CompanyPrivate;


use App\Invoice\Company\CompanyRepository;
use App\Invoice\CompanyPrivate\CompanyPrivateService;
use App\Invoice\CompanyPrivate\CompanyPrivateRepository;
use App\Invoice\Entity\CompanyPrivate;
use App\Invoice\Setting\SettingRepository;
use App\Service\WebControllerService;
use App\User\UserService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Yiisoft\Http\Method;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Session\Flash\Flash;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\View\ViewRenderer;
use \Exception;

final class CompanyPrivateController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService;
    private CompanyPrivateService $companyprivateService;
    private TranslatorInterface $translator;
        
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UserService $userService,
        CompanyPrivateService $companyprivateService,
        TranslatorInterface $translator
    )    
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/companyprivate')
                                           ->withLayout(dirname(dirname(__DIR__)).'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->userService = $userService;
        $this->companyprivateService = $companyprivateService;
        $this->translator = $translator;
    }
    
    public function index(SessionInterface $session, CompanyPrivateRepository $companyprivateRepository, SettingRepository $settingRepository, Request $request, CompanyPrivateService $service): Response
    {      
          $canEdit = $this->rbac($session);
          $flash = $this->flash($session, '','');
          $parameters = [
            's'=>$settingRepository,
            'canEdit' => $canEdit,
            'companyprivates' => $this->companyprivates($companyprivateRepository),
            'company_private'=>$this->translator->translate('invoice.company.private'),
            'flash'=> $flash
         ];
        
        return $this->viewRenderer->render('index', $parameters);
    }
    
    public function add(ViewRenderer $head,SessionInterface $session, Request $request, 
                        ValidatorInterface $validator,
                        SettingRepository $settingRepository,                        
                        CompanyRepository $companyRepository
    )
    {
        $this->rbac($session);
        $parameters = [
            'title' => 'Add',
            'action' => ['companyprivate/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$settingRepository,
            'head'=>$head,            
            'companies'=>$companyRepository->findAllPreloaded(),            
            'company_public'=>$this->translator->translate('invoice.company.public'),
        ];
        
        if ($request->getMethod() === Method::POST) {
            
            $form = new CompanyPrivateForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->companyprivateService->saveCompanyPrivate(new CompanyPrivate(),$form);
                return $this->webService->getRedirectResponse('companyprivate/index');
            }
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function edit(ViewRenderer $head, SessionInterface $session, Request $request, CurrentRoute $currentRoute,
                        ValidatorInterface $validator,
                        CompanyPrivateRepository $companyprivateRepository, 
                        SettingRepository $settingRepository,                        
                        CompanyRepository $companyRepository
    ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => 'Edit',
            'action' => ['companyprivate/edit', ['id' => $this->companyprivate($currentRoute, $companyprivateRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->companyprivate($currentRoute, $companyprivateRepository)),
            'head'=>$head,
            's'=>$settingRepository,
            'companies'=>$companyRepository->findAllPreloaded()
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new CompanyPrivateForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->companyprivateService->saveCompanyPrivate($this->companyprivate($currentRoute,$companyprivateRepository), $form);
                return $this->webService->getRedirectResponse('companyprivate/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function delete(SessionInterface $session,CurrentRoute $currentRoute,CompanyPrivateRepository $companyprivateRepository 
    ): Response {
        $this->rbac($session);
        try {
              if ($this->companyprivateService->deleteCompanyPrivate($this->companyprivate($currentRoute, $companyprivateRepository))) {
                $this->flash($session, 'info', 'Deleted.');
                return $this->webService->getRedirectResponse('companyprivate/index'); } else 
            {
                $this->flash($session, 'warning', 'Not deleted because there is existing public company information, and perhaps a profile attached.');
                return $this->webService->getRedirectResponse('companyprivate/index');   
            }
	} catch (Exception $e) {
            unset($e);
            $this->flash($session, 'danger', $e);
            return $this->webService->getRedirectResponse('companyprivate/index'); 
        }
    }
    
    public function view(SessionInterface $session, CurrentRoute $currentRoute, CompanyPrivateRepository $companyprivateRepository,
        SettingRepository $settingRepository,
        ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('view'),
            'action' => ['companyprivate/view', ['id' => $this->companyprivate($currentRoute, $companyprivateRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->companyprivate($currentRoute, $companyprivateRepository)),
            's'=>$settingRepository,             
            'companyprivate'=>$companyprivateRepository->repoCompanyPrivatequery($this->companyprivate($currentRoute, $companyprivateRepository)->getId()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }
        
    private function rbac(SessionInterface $session) 
    {
        $canEdit = $this->userService->hasPermission('editCompanyPrivate');
        if (!$canEdit){
            $this->flash($session,'warning', $this->translator->translate('invoice.permission'));
            return $this->webService->getRedirectResponse('companyprivate/index');
        }
        return $canEdit;
    }
    
    private function companyprivate(CurrentRoute $currentRoute, CompanyPrivateRepository $companyprivateRepository) 
    {
        $id = $currentRoute->getArgument('id');       
        $companyprivate = $companyprivateRepository->repoCompanyPrivatequery($id);
        if ($companyprivate === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $companyprivate;
    }
    
    private function companyprivates(CompanyPrivateRepository $companyprivateRepository) 
    {
        $companyprivates = $companyprivateRepository->findAllPreloaded();        
        if ($companyprivates === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $companyprivates;
    }
    
    private function body($companyprivate) {
        $body = [                
                    'id'=>$companyprivate->getId(),
                    'company_id'=>$companyprivate->getCompany_id(),
                    'vat_id'=>$companyprivate->getVat_id(),
                    'tax_code'=>$companyprivate->getTax_code(),
                    'iban'=>$companyprivate->getIban(),
                    'gln'=>$companyprivate->getGln(),
                    'rcc'=>$companyprivate->getRcc()
                ];
        return $body;
    }
    
    private function flash(SessionInterface $session, $level, $message){
        $flash = new Flash($session);
        $flash->set($level, $message); 
        return $flash;
    }
}

