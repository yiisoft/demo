<?php

declare(strict_types=1); 

namespace App\Invoice\InvTaxRate;

use App\Invoice\Entity\InvTaxRate;
use App\Invoice\InvTaxRate\InvTaxRateService;
use App\Invoice\InvTaxRate\InvTaxRateRepository;
use App\Invoice\Setting\SettingRepository;
use App\Invoice\Inv\InvRepository;
use App\Invoice\TaxRate\TaxRateRepository;
use App\User\UserService;
use Yiisoft\Validator\ValidatorInterface;
use App\Service\WebControllerService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Http\Method;
use Yiisoft\Yii\View\ViewRenderer;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Session\Flash\Flash;

final class InvTaxRateController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService;
    private InvTaxRateService $invtaxrateService;
    
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UserService $userService,
        InvTaxRateService $invtaxrateService
    )    
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/invtaxrate')
                                           ->withLayout(dirname(dirname(__DIR__)) .'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->userService = $userService;
        $this->invtaxrateService = $invtaxrateService;
    }
    
    public function index(SessionInterface $session, InvTaxRateRepository $invtaxrateRepository, SettingRepository $settingRepository, Request $request, InvTaxRateService $service): Response
    {
       
         $canEdit = $this->rbac($session);
         $flash = $this->flash($session, 'dummy' , 'Flash message!.');
         $parameters = [
      
          's'=>$settingRepository,
          'canEdit' => $canEdit,
          'invtaxrates' => $this->invtaxrates($invtaxrateRepository),
          'flash'=> $flash
         ];

        if ($this->isAjaxRequest($request)) {
            return $this->viewRenderer->renderPartial('_invtaxrates', ['data' => $paginator]);
        }
        
        return $this->viewRenderer->render('index', $parameters);
    }
    
    private function isAjaxRequest(Request $request): bool
    {
        return $request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
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
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function edit(ViewRenderer $head, SessionInterface $session, Request $request, 
                        ValidatorInterface $validator,
                        InvTaxRateRepository $invtaxrateRepository, 
                        SettingRepository $settingRepository,                        
                        InvRepository $invRepository,
                        TaxRateRepository $tax_rateRepository
    ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => 'Edit',
            'action' => ['invtaxrate/edit', ['id' => $this->invtaxrate($request, $invtaxrateRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->invtaxrate($request, $invtaxrateRepository)),
            'head'=>$head,
            's'=>$settingRepository,
                        'invs'=>$invRepository->findAllPreloaded(),
            'tax_rates'=>$tax_rateRepository->findAllPreloaded()
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new InvTaxRateForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->invtaxrateService->saveInvTaxRate($this->invtaxrate($request,$invtaxrateRepository), $form);
                return $this->webService->getRedirectResponse('invtaxrate/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function delete(SessionInterface $session,Request $request,InvTaxRateRepository $invtaxrateRepository 
    ): Response {
        $this->rbac($session);
       
        $this->invtaxrateService->deleteInvTaxRate($this->invtaxrate($request,$invtaxrateRepository));               
        return $this->webService->getRedirectResponse('invtaxrate/index');        
    }
    
    public function view(SessionInterface $session,Request $request,InvTaxRateRepository $invtaxrateRepository,
        SettingRepository $settingRepository
        ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('view'),
            'action' => ['invtaxrate/edit', ['id' => $this->invtaxrate($request, $invtaxrateRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->invtaxrate($request, $invtaxrateRepository)),
            's'=>$settingRepository,             
            'invtaxrate'=>$invtaxrateRepository->repoInvTaxRatequery($this->invtaxrate($request, $invtaxrateRepository)->getId()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }
    
    private function rbac(SessionInterface $session) 
    {
        $canEdit = $this->userService->hasPermission('editInvTaxRate');
        if (!$canEdit){
            $this->flash($session,'warning', 'You do not have the required permission.');
            return $this->webService->getRedirectResponse('invtaxrate/index');
        }
        return $canEdit;
    }
    
    private function invtaxrate(Request $request,InvTaxRateRepository $invtaxrateRepository) 
    {
        $id = $request->getAttribute('id');       
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
        };
        return $invtaxrates;
    }
    
    private function body($invtaxrate) {
        $body = [
                
          'id'=>$invtaxrate->getId(),
          'inv_id'=>$invtaxrate->getInv_id(),
          'tax_rate_id'=>$invtaxrate->getTax_rate_id(),
          'include_item_tax'=>$invtaxrate->getInclude_item_tax(),
          'amount'=>$invtaxrate->getAmount()
                ];
        return $body;
    }
    
    private function flash(SessionInterface $session, $level, $message){
        $flash = new Flash($session);
        $flash->set($level, $message); 
        return $flash;
    }
}

?>