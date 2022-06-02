<?php

declare(strict_types=1); 

namespace App\Invoice\Company;

use App\Invoice\Company\CompanyService;
use App\Invoice\Company\CompanyRepository;
use App\Invoice\Entity\Company;
use App\Invoice\Setting\SettingRepository;
use App\Service\WebControllerService;
use App\User\UserService;

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

final class CompanyController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService;
    private CompanyService $companyService;
    private TranslatorInterface $translator;
        
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UserService $userService,
        CompanyService $companyService,
        TranslatorInterface $translator
    )    
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/company')
                                           ->withLayout(dirname(dirname(__DIR__)) .'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->userService = $userService;
        $this->companyService = $companyService;
        $this->translator = $translator;
    }
    
    public function index(SessionInterface $session, CompanyRepository $companyRepository, SettingRepository $settingRepository, Request $request, CompanyService $service): Response
    {      
         $canEdit = $this->rbac($session);
         $flash = $this->flash($session, '','');
         $parameters = [
          's'=>$settingRepository,
          'canEdit' => $canEdit,
          'companies' => $this->companies($companyRepository),
          'company_public'=>$this->translator->translate('invoice.company.public'),   
          'flash'=> $flash
         ];
        return $this->viewRenderer->render('index', $parameters);
    }
    
    public function add(ViewRenderer $head,SessionInterface $session, Request $request, 
                        ValidatorInterface $validator,
                        SettingRepository $settingRepository,                   
    )
    {
        $this->rbac($session);
        $parameters = [
            'title' => 'Add',
            'action' => ['company/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$settingRepository,
            'company_public'=>$this->translator->translate('invoice.company.public'),
            'head'=>$head,
            
        ];
        
        if ($request->getMethod() === Method::POST) {
            $form = new CompanyForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->companyService->saveCompany(new Company(),$form);
                return $this->webService->getRedirectResponse('company/index');
            }
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function edit(ViewRenderer $head, SessionInterface $session, Request $request, 
                        ValidatorInterface $validator,
                        CompanyRepository $companyRepository, 
                        SettingRepository $settingRepository,
                        CurrentRoute $currentRoute

    ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => 'Edit',
            'action' => ['company/edit', ['id' => $this->company($currentRoute, $companyRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->company($currentRoute, $companyRepository)),
            'head'=>$head,
            'company_public'=>$this->translator->translate('invoice.company.public'),
            's'=>$settingRepository,
            
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new CompanyForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->companyService->saveCompany($this->company($currentRoute,$companyRepository), $form);
                return $this->webService->getRedirectResponse('company/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function delete(SessionInterface $session,CurrentRoute $currentRoute, CompanyRepository $companyRepository 
    ): Response {
        $this->rbac($session);
        try {
          if ($this->companyService->deleteCompany($this->company($currentRoute, $companyRepository))) {               
            $this->flash($session, 'info', 'Deleted.'); 
            return $this->webService->getRedirectResponse('company/index'); 
          } else {
            $this->flash($session, 'warning', 'Not deleted because you have a profiel attached.');
            return $this->webService->getRedirectResponse('company/index');   
          }  
	} catch (Exception $e) {
            unset($e);
            $this->flash($session, 'danger', $e);
            return $this->webService->getRedirectResponse('company/index'); 
        }
    }
    
    public function view(SessionInterface $session,CurrentRoute $currentRoute, CompanyRepository $companyRepository,
        SettingRepository $settingRepository,
        ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('view'),
            'action' => ['company/view', ['id' => $this->company($currentRoute, $companyRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->company($currentRoute, $companyRepository)),
            's'=>$settingRepository,             
            'company'=>$companyRepository->repoCompanyquery($this->company($currentRoute, $companyRepository)->getId()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }
        
    private function rbac(SessionInterface $session) 
    {
        $canEdit = $this->userService->hasPermission('editCompany');
        if (!$canEdit){
            $this->flash($session,'warning', $this->translator->translate('invoice.permission')); 
            return $this->webService->getRedirectResponse('company/index');
        }
        return $canEdit;
    }
    
    private function company(CurrentRoute $currentRoute, CompanyRepository $companyRepository) 
    {
        $id = $currentRoute->getArgument('id');       
        $company = $companyRepository->repoCompanyquery($id);
        if ($company === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $company;
    }
    
    private function companies(CompanyRepository $companyRepository) 
    {
        $companies = $companyRepository->findAllPreloaded();        
        if ($companies === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $companies;
    }
    
    private function body($company) {
        $body = [
                
          'id'=>$company->getId(),
          'current'=>$company->getCurrent(),
          'name'=>$company->getName(),
          'address_1'=>$company->getAddress_1(),
          'address_2'=>$company->getAddress_2(),
          'city'=>$company->getCity(),
          'state'=>$company->getState(),
          'zip'=>$company->getZip(),
          'country'=>$company->getCountry(),
          'phone'=>$company->getPhone(),
          'fax'=>$company->getFax(),
          'email'=>$company->getEmail(),
          'web'=>$company->getWeb(),
          'date_created'=>$company->getDate_created(),
          'date_modified'=>$company->getDate_modified()
                ];
        return $body;
    }
    
    private function flash(SessionInterface $session, $level, $message){
        $flash = new Flash($session);
        $flash->set($level, $message); 
        return $flash;
    }
}

