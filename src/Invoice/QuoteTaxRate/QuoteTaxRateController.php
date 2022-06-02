<?php

declare(strict_types=1); 

namespace App\Invoice\QuoteTaxRate;

use App\Invoice\Entity\QuoteTaxRate;
use App\Invoice\Quote\QuoteRepository;
use App\Invoice\QuoteTaxRate\QuoteTaxRateService;
use App\Invoice\QuoteTaxRate\QuoteTaxRateRepository;
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

final class QuoteTaxRateController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService;
    private QuoteTaxRateService $quotetaxrateService;
    private DataResponseFactoryInterface $factory;
    private UrlGenerator $urlGenerator;
    private TranslatorInterface $translator;
    
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UserService $userService,
        QuoteTaxRateService $quotetaxrateService,
        DataResponseFactoryInterface $factory,              
        UrlGenerator $urlGenerator,
        TranslatorInterface $translator,
    )    
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/quotetaxrate')
                                           ->withLayout(dirname(dirname(__DIR__)) .'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->userService = $userService;
        $this->quotetaxrateService = $quotetaxrateService;
        $this->factory = $factory;        
        $this->urlGenerator = $urlGenerator;   
        $this->translator = $translator;
    }
    
    public function index(SessionInterface $session, QuoteTaxRateRepository $quotetaxrateRepository, SettingRepository $settingRepository, Request $request, QuoteTaxRateService $service): Response
    {
       
         $canEdit = $this->rbac($session);
         $flash = $this->flash($session, '','');
         $parameters = [
      
          's'=>$settingRepository,
          'canEdit' => $canEdit,
          'quotetaxrates' => $this->quotetaxrates($quotetaxrateRepository),
          'flash'=> $flash
         ];

        return $this->viewRenderer->render('index', $parameters);
    }
    
    private function isAjaxRequest(Request $request): bool
    {
        return $request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
    }
    
    public function add(ViewRenderer $head,SessionInterface $session, Request $request, 
                        ValidatorInterface $validator,
                        SettingRepository $settingRepository,                        
                        QuoteRepository $quoteRepository,
                        TaxRateRepository $tax_rateRepository
    )
    {
        $this->rbac($session);
        $parameters = [
            'title' => 'Add',
            'action' => ['quotetaxrate/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$settingRepository,
            'head'=>$head,
            'quotes'=>$quoteRepository->findAllPreloaded(),
            'tax_rates'=>$tax_rateRepository->findAllPreloaded(),
        ];
        
        if ($request->getMethod() === Method::POST) {
            $form = new QuoteTaxRateForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->quotetaxrateService->saveQuoteTaxRate(new QuoteTaxRate(),$form);
                return $this->webService->getRedirectResponse('quotetaxrate/index');
            }
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function edit(ViewRenderer $head, SessionInterface $session, Request $request, CurrentRoute $currentRoute,
                        ValidatorInterface $validator,
                        QuoteTaxRateRepository $quotetaxrateRepository, 
                        SettingRepository $settingRepository,                        
                        QuoteRepository $quoteRepository,
                        TaxRateRepository $tax_rateRepository
    ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => 'Edit',
            'action' => ['quotetaxrate/edit', ['id' => $this->quotetaxrate($currentRoute, $quotetaxrateRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->quotetaxrate($currentRoute, $quotetaxrateRepository)),
            'head'=>$head,
            's'=>$settingRepository,
            'quotes'=>$quoteRepository->findAllPreloaded(),
            'tax_rates'=>$tax_rateRepository->findAllPreloaded()
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new QuoteTaxRateForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->quotetaxrateService->saveQuoteTaxRate($this->quotetaxrate($currentRoute, $quotetaxrateRepository), $form);
                return $this->webService->getRedirectResponse('quotetaxrate/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function delete(SessionInterface $session, CurrentRoute $currentRoute, QuoteTaxRateRepository $quotetaxrateRepository 
    ): Response {
        $this->rbac($session);
        try {
            $this->quotetaxrateService->deleteQuoteTaxRate($this->quotetaxrate($currentRoute,$quotetaxrateRepository));
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
        return $this->factory->createResponse(Status::FOUND)->withHeader(Header::LOCATION, $this->urlGenerator->generate('quote/view',['id'=>$this->quotetaxrate($currentRoute,$quotetaxrateRepository)->getQuote_id()]));        
    }
    
    public function view(SessionInterface $session, CurrentRoute $currentRoute, QuoteTaxRateRepository $quotetaxrateRepository,
        SettingRepository $settingRepository,
        ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('view'),
            'action' => ['quotetaxrate/edit', ['id' => $this->quotetaxrate($currentRoute, $quotetaxrateRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->quotetaxrate($currentRoute, $quotetaxrateRepository)),
            's'=>$settingRepository,             
            'quotetaxrate'=>$quotetaxrateRepository->repoQuoteTaxRatequery($this->quotetaxrate($currentRoute, $quotetaxrateRepository)->getId()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }
    
    private function rbac(SessionInterface $session) 
    {
        $canEdit = $this->userService->hasPermission('editQuoteTaxRate');
        if (!$canEdit){
            $this->flash($session,'warning', $this->translator->translate('invoice.permission'));
            return $this->webService->getRedirectResponse('quotetaxrate/index');
        }
        return $canEdit;
    }
    
    private function quotetaxrate(CurrentRoute $currentRoute, QuoteTaxRateRepository $quotetaxrateRepository) 
    {
        $id = $currentRoute->getArgument('id');       
        $quotetaxrate = $quotetaxrateRepository->repoQuoteTaxRatequery($id);
        if ($quotetaxrate === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $quotetaxrate;
    }
    
    private function quotetaxrates(QuoteTaxRateRepository $quotetaxrateRepository) 
    {
        $quotetaxrates = $quotetaxrateRepository->findAllPreloaded();        
        if ($quotetaxrates === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $quotetaxrates;
    }
    
    private function body($quotetaxrate) {
        $body = [
                
          'id'=>$quotetaxrate->getId(),
          'quote_id'=>$quotetaxrate->getQuote_id(),
          'tax_rate_id'=>$quotetaxrate->getTax_rate_id(),
          'include_item_tax'=>$quotetaxrate->getInclude_item_tax(),
          'quote_tax_rate_amount'=>$quotetaxrate->getQuote_tax_rate_amount()
                ];
        return $body;
    }
    
    private function flash(SessionInterface $session, $level, $message){
        $flash = new Flash($session);
        $flash->set($level, $message); 
        return $flash;
    }
}