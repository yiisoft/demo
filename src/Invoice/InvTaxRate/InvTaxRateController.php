<?php

declare(strict_types=1); 

namespace App\Invoice\InvTaxRate;

use App\Invoice\Entity\InvTaxRate;
use App\Invoice\Inv\InvRepository;
use App\Invoice\InvTaxRate\InvTaxRateService;
use App\Invoice\InvTaxRate\InvTaxRateRepository;
use App\Invoice\Setting\SettingRepository;
use App\Invoice\TaxRate\TaxRateRepository;

use App\User\UserService;
use App\Service\WebControllerService;


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Yiisoft\DataResponse\DataResponseFactoryInterface;
use Yiisoft\Http\Method;
use Yiisoft\Http\Header;
use Yiisoft\Http\Status;
use Yiisoft\Router\FastRoute\UrlGenerator;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Session\Flash\Flash;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\View\ViewRenderer;

final class InvTaxRateController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService;
    private InvTaxRateService $invtaxrateService;
    private DataResponseFactoryInterface $factory;
    private UrlGenerator $urlGenerator;
    private TranslatorInterface $translator;

    
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UserService $userService,
        InvTaxRateService $invtaxrateService,
        DataResponseFactoryInterface $factory,              
        UrlGenerator $urlGenerator,
        TranslatorInterface $translator
    )    
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/invtaxrate')
                                           ->withLayout(dirname(dirname(__DIR__)) .'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->userService = $userService;
        $this->invtaxrateService = $invtaxrateService;
        $this->factory = $factory;        
        $this->urlGenerator = $urlGenerator;   
        $this->translator = $translator;
    }
    
    public function index(SessionInterface $session, InvTaxRateRepository $invtaxrateRepository, SettingRepository $settingRepository): Response
    {
       
         $canEdit = $this->rbac($session);
         $flash = $this->flash($session, '','');
         $parameters = [
      
          's'=>$settingRepository,
          'canEdit' => $canEdit,
          'invtaxrates' => $this->invtaxrates($invtaxrateRepository),
          'flash'=> $flash
         ];

        return $this->viewRenderer->render('index', $parameters);
    }
    
    public function add(ViewRenderer $head,SessionInterface $session, Request $request, 
                        ValidatorInterface $validator,
                        SettingRepository $settingRepository,                        
                        InvRepository $invRepository,
                        TaxRateRepository $tax_rateRepository
    )
    {
        $this->rbac($session);
        $parameters = [
            'title' => 'Add',
            'action' => ['invtaxrate/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$settingRepository,
            'head'=>$head,
            'invs'=>$invRepository->findAllPreloaded(),
            'tax_rates'=>$tax_rateRepository->findAllPreloaded(),
        ];
        
        if ($request->getMethod() === Method::POST) {
            $form = new InvTaxRateForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->invtaxrateService->saveInvTaxRate(new InvTaxRate(),$form);
                return $this->webService->getRedirectResponse('invtaxrate/index');
            }
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function edit(ViewRenderer $head, SessionInterface $session, Request $request, CurrentRoute $currentRoute,
                        ValidatorInterface $validator,
                        InvTaxRateRepository $invtaxrateRepository, 
                        SettingRepository $settingRepository,                        
                        InvRepository $invRepository,
                        TaxRateRepository $tax_rateRepository
    ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => 'Edit',
            'action' => ['invtaxrate/edit', ['id' => $this->invtaxrate($currentRoute, $invtaxrateRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->invtaxrate($currentRoute, $invtaxrateRepository)),
            'head'=>$head,
            's'=>$settingRepository,
            'invs'=>$invRepository->findAllPreloaded(),
            'tax_rates'=>$tax_rateRepository->findAllPreloaded()
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new InvTaxRateForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->invtaxrateService->saveInvTaxRate($this->invtaxrate($currentRoute, $invtaxrateRepository), $form);
                return $this->webService->getRedirectResponse('invtaxrate/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function delete(SessionInterface $session, CurrentRoute $currentRoute, InvTaxRateRepository $invtaxrateRepository 
    ): Response {
        $this->rbac($session);
        try {
            $this->invtaxrateService->deleteInvTaxRate($this->invtaxrate($currentRoute,$invtaxrateRepository));
            $this->flash($session, 'info', 'Deleted.');
            $parameters = [
                      'success' => 1
            ];
        } catch (Exception $e) {
            unset($e);
            $this->flash($session, 'danger', 'Cannot delete.');
            $parameters = [
                  'success' => 0
            ];
        }
        return $this->factory->createResponse(Status::FOUND)->withHeader(Header::LOCATION, $this->urlGenerator->generate('inv/view',['id'=>$this->invtaxrate($currentRoute,$invtaxrateRepository)->getInv_id()])); 
        //return $this->factory->createResponse(Json::encode($parameters));                      
    }
    
    public function view(SessionInterface $session, CurrentRoute $currentRoute, InvTaxRateRepository $invtaxrateRepository,
        SettingRepository $settingRepository,
        ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('view'),
            'action' => ['invtaxrate/edit', ['id' => $this->invtaxrate($currentRoute, $invtaxrateRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->invtaxrate($currentRoute, $invtaxrateRepository)),
            's'=>$settingRepository,             
            'invtaxrate'=>$invtaxrateRepository->repoInvTaxRatequery($this->invtaxrate($currentRoute, $invtaxrateRepository)->getId()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }
    
    private function rbac(SessionInterface $session) 
    {
        $canEdit = $this->userService->hasPermission('editInvTaxRate');
        if (!$canEdit){
            $this->flash($session,'warning', $this->translator->translate('invoice.permission'));
            return $this->webService->getRedirectResponse('invtaxrate/index');
        }
        return $canEdit;
    }
    
    private function invtaxrate(CurrentRoute $currentRoute, InvTaxRateRepository $invtaxrateRepository) 
    {
        $id = $currentRoute->getArgument('id');       
        $invtaxrate = $invtaxrateRepository->repoInvTaxRatequery($id);
        if ($invtaxrate === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $invtaxrate;
    }
    
    private function invtaxrates(InvTaxRateRepository $invtaxrateRepository) 
    {
        $invtaxrates = $invtaxrateRepository->findAllPreloaded();        
        if ($invtaxrates === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $invtaxrates;
    }
    
    private function body($invtaxrate) {
        $body = [
                
          'id'=>$invtaxrate->getId(),
          'inv_id'=>$invtaxrate->getInv_id(),
          'tax_rate_id'=>$invtaxrate->getTax_rate_id(),
          'include_item_tax'=>$invtaxrate->getInclude_item_tax(),
          'inv_tax_rate_amount'=>$invtaxrate->getInv_tax_rate_amount()
                ];
        return $body;
    }
    
    private function flash(SessionInterface $session, $level, $message){
        $flash = new Flash($session);
        $flash->set($level, $message); 
        return $flash;
    }
}