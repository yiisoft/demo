<?php

declare(strict_types=1);

namespace App\Invoice\TaxRate;

use App\Invoice\Entity\TaxRate;
use App\Invoice\TaxRate\TaxRateRepository;
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

final class TaxRateController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private TaxRateService $taxrateService;       
    private UserService $userService;

    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        TaxRateService $taxrateService,
        UserService $userService    
    ) {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/taxrate')
                                           ->withLayout(dirname(dirname(__DIR__)).'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->taxrateService = $taxrateService;        
        $this->userService = $userService;
    }

    public function index(Session $session,TaxRateRepository $taxrateRepository, SettingRepository $settingRepository): Response
    {
        $canEdit = $this->rbac($session);
        $taxrates = $this->taxrates($taxrateRepository); 
        $flash = $this->flash($session, 'success', 'Help information will appear here.');
        $parameters = [
            's'=>$settingRepository,
            'canEdit' => $canEdit,
            'taxrates' => $taxrates, 
            'flash'=>$flash,
        ]; 
        return $this->viewRenderer->render('index', $parameters);
    }

    public function add(Session $session, Request $request,SettingRepository $settingRepository,ValidatorInterface $validator): Response
    {
        $this->rbac($session);
        $parameters = [
            'title' => 'Add Tax Rate',
            'action' => ['taxrate/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$settingRepository
        ];
        
        if ($request->getMethod() === Method::POST) {
            $form = new TaxRateForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->taxrateService->saveTaxRate(new TaxRate(), $form);
                return $this->webService->getRedirectResponse('taxrate/index');
            }
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('__form', $parameters);
    }

    public function edit(Session $session, Request $request, SettingRepository $settingRepository, TaxRateRepository $taxrateRepository, ValidatorInterface $validator): Response 
    {
        $this->rbac($session);
        $taxrate = $this->taxrate($request, $taxrateRepository);
        $parameters = [
            'title' => 'Edit Tax Rate',
            'action' => ['taxrate/edit', ['tax_rate_id' => $taxrate->getTax_rate_id()]],
            'errors' => [],
            'body' => [
                'tax_rate_name' => $taxrate->getTax_rate_name(),
                'tax_rate_percent'=>$taxrate->getTax_rate_percent(),
            ],
            's'=>$settingRepository,
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new TaxRateForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->taxrateService->saveTaxRate($taxrate, $form);
                return $this->webService->getRedirectResponse('taxrate/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('__form', $parameters);
    }
    
    public function delete(Session $session, Request $request, TaxRateRepository $taxrateRepository): Response 
    {
        $this->rbac($session);
        $taxrate = $this->taxrate($request,$taxrateRepository);
        $this->flash($session,'danger','This record has been deleleted.');
        $this->taxrateService->deleteTaxRate($taxrate);               
        return $this->webService->getRedirectResponse('taxrate/index');        
    }
    
    public function view(Session $session,Request $request,TaxRateRepository $taxrateRepository,SettingRepository $settingRepository,ValidatorInterface $validator): Response {
        $this->rbac($session);        
        $taxrate = $this->taxrate($request, $taxrateRepository);
        $parameters = [
            'title' => 'Edit Tax Rate',
            'action' => ['taxrate/edit', ['tax_rate_id' => $taxrate->getTax_rate_id()]],
            'errors' => [],
            'taxrate'=>$taxrate,
            's'=>$settingRepository,     
            'body' => [
                'tax_rate_id'=>$taxrate->getTax_rate_id(),
                'tax_rate_name'=>$taxrate->getTax_rate_name(),
                'tax_rate_percent'=>$taxrate->getTax_rate_percent()
            ],            
        ];
        return $this->viewRenderer->render('__view', $parameters);
    }
    
    //$canEdit = $this->rbac();
    private function rbac(Session $session) {
        $canEdit = $this->userService->hasPermission('editTaxrate');
        if (!$canEdit){
            $this->flash($session,'warning', 'You do not have the required permission.');
            return $this->webService->getRedirectResponse('taxrate/index');
        }
        return $canEdit;
    }
    
    //$taxrate = $this->taxrate();
    private function taxrate(Request $request, TaxRateRepository $taxrateRepository){
        $tax_rate_id = $request->getAttribute('tax_rate_id');
        $taxrate = $taxrateRepository->repoTaxRatequery($tax_rate_id);
        if ($taxrate === null) {
            return $this->webService->getNotFoundResponse();
        }        
        return $taxrate; 
    }
    
    //$taxrates = $this->taxrates();
    private function taxrates(TaxRateRepository $taxrateRepository){
        $taxrates = $taxrateRepository->findAllPreloaded();
        if ($taxrates === null) {
            return $this->webService->getNotFoundResponse();
        };
        return $taxrates;
    }
    
    //$this->flash
    private function flash(Session $session, $level, $message){
        $flash = new Flash($session);
        $flash->set($level, $message); 
        return $flash;
    }
}
