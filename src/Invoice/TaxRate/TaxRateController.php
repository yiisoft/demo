<?php

declare(strict_types=1);

namespace App\Invoice\TaxRate;

use App\Invoice\Entity\TaxRate;
use App\Invoice\TaxRate\TaxRateRepository;
use App\Invoice\Setting\SettingRepository;
use App\Service\WebControllerService;
use App\User\UserService;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Http\Method;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Session\Flash\Flash;
use Yiisoft\Session\SessionInterface as Session;
use Yiisoft\Translator\TranslatorInterface as Translator; 
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\View\ViewRenderer;

use \Exception;

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
        UserService $userService,
        Translator $translator,
    ) {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/taxrate')
                                           ->withLayout(dirname(dirname(__DIR__)).'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->taxrateService = $taxrateService;        
        $this->userService = $userService;
        $this->translator = $translator;
    }

    public function index(Session $session, TaxRateRepository $taxrateRepository, SettingRepository $settingRepository, Request $request, TaxRateService $service): Response
    {      
        $pageNum = (int)$request->getAttribute('page', '1');
        $paginator = (new OffsetPaginator($this->taxrates($taxrateRepository)))
        ->withPageSize((int)$settingRepository->setting('default_list_limit'))
        ->withCurrentPage($pageNum);
      
        $canEdit = $this->rbac($session);
        $flash = $this->flash($session, '','');
        $parameters = [
              'paginator' => $paginator,  
              's'=>$settingRepository,
              'canEdit' => $canEdit,
              'taxrates' => $this->taxrates($taxrateRepository),
              'flash'=> $flash
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
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('__form', $parameters);
    }

    public function edit(ViewRenderer $head,Session $session, Request $request, CurrentRoute $currentRoute,
            SettingRepository $settingRepository, TaxRateRepository $taxrateRepository, ValidatorInterface $validator): Response 
    {
        $this->rbac($session);
        $taxrate = $this->taxrate($currentRoute, $taxrateRepository);
        $parameters = [
            'title' => $settingRepository->trans('edit'),
            'action' => ['taxrate/edit', ['tax_rate_id' => $taxrate->getTax_rate_id()]],
            'errors' => [],
            'head'=>$head,
            'translator'=>$this->translator,
            'body' => [
                'tax_rate_name' => $taxrate->getTax_rate_name(),
                'tax_rate_percent'=>$taxrate->getTax_rate_percent(),
                'tax_rate_default'=>$taxrate->getTax_rate_default(),
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
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('__form', $parameters);
    }
    
    public function delete(Session $session, CurrentRoute $currentRoute, TaxRateRepository $taxrateRepository): Response 
    {
        try {
            $this->rbac($session);
            $taxrate = $this->taxrate($currentRoute, $taxrateRepository);
            $this->taxrateService->deleteTaxRate($taxrate);               
            return $this->webService->getRedirectResponse('taxrate/index'); 
	} catch (Exception $e) {
            unset($e);
            $this->flash($session, 'danger', 'Cannot delete. Tax Rate history exists.');
            return $this->webService->getRedirectResponse('taxrate/index');
        } 
    }
    
    public function view(Session $session, CurrentRoute $currentRoute, TaxRateRepository $taxrateRepository,SettingRepository $settingRepository,ValidatorInterface $validator): Response {
        $this->rbac($session);        
        $taxrate = $this->taxrate($currentRoute, $taxrateRepository);
        $parameters = [
            'title' => 'Edit Tax Rate',
            'action' => ['taxrate/edit', ['tax_rate_id' => $taxrate->getTax_rate_id()]],
            'errors' => [],
            'taxrate'=>$taxrate,
            's'=>$settingRepository,
            'translator'=>$this->translator,
            'body' => [
                'tax_rate_id'=>$taxrate->getTax_rate_id(),
                'tax_rate_name'=>$taxrate->getTax_rate_name(),
                'tax_rate_percent'=>$taxrate->getTax_rate_percent(),
                'default'=>$taxrate->getDefault()
            ],            
        ];
        return $this->viewRenderer->render('__view', $parameters);
    }
    
    //$canEdit = $this->rbac();
    private function rbac(Session $session) {
        $canEdit = $this->userService->hasPermission('editTaxrate');
        if (!$canEdit){
            $this->flash($session,'warning', $this->translator->translate('invoice.permission'));
            return $this->webService->getRedirectResponse('taxrate/index');
        }
        return $canEdit;
    }
    
    //$taxrate = $this->taxrate();
    private function taxrate(CurrentRoute $currentRoute, TaxRateRepository $taxrateRepository){
        $tax_rate_id = $currentRoute->getArgument('tax_rate_id');
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
        }
        return $taxrates;
    }
    
    //$this->flash
    private function flash(Session $session, $level, $message){
        $flash = new Flash($session);
        $flash->set($level, $message); 
        return $flash;
    }
}