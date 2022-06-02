<?php

declare(strict_types=1); 

namespace App\Invoice\QuoteCustom;

use App\Invoice\CustomField\CustomFieldRepository;
use App\Invoice\Entity\QuoteCustom;
use App\Invoice\Quote\QuoteRepository;
use App\Invoice\QuoteCustom\QuoteCustomService;
use App\Invoice\QuoteCustom\QuoteCustomRepository;
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

final class QuoteCustomController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService;
    private QuoteCustomService $quotecustomService;
    private TranslatorInterface $translator;
        
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UserService $userService,
        QuoteCustomService $quotecustomService,
        TranslatorInterface $translator,
    )    
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/quotecustom')
                                           ->withLayout(dirname(dirname(__DIR__)).'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->userService = $userService;
        $this->quotecustomService = $quotecustomService;
        $this->translator = $translator;
    }
    
    public function index(SessionInterface $session, QuoteCustomRepository $quotecustomRepository, SettingRepository $settingRepository, Request $request, QuoteCustomService $service): Response
    {      
         $canEdit = $this->rbac($session);
         $flash = $this->flash($session, '','');
         $parameters = [
          's'=>$settingRepository,
          'canEdit' => $canEdit,
          'quotecustoms' => $this->quotecustoms($quotecustomRepository),
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
                        CustomFieldRepository $custom_fieldRepository,
                        QuoteRepository $quoteRepository
    )
    {
        $this->rbac($session);
        $parameters = [
            'title' => 'Add',
            'action' => ['quotecustom/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$settingRepository,
            'head'=>$head,
            
            'custom_fields'=>$custom_fieldRepository->findAllPreloaded(),
            'quotes'=>$quoteRepository->findAllPreloaded(),
        ];
        
        if ($request->getMethod() === Method::POST) {
            
            $form = new QuoteCustomForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->quotecustomService->saveQuoteCustom(new QuoteCustom(),$form);
                return $this->webService->getRedirectResponse('quotecustom/index');
            }
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function edit(ViewRenderer $head, SessionInterface $session, Request $request, CurrentRoute $currentRoute,
                        ValidatorInterface $validator,
                        QuoteCustomRepository $quotecustomRepository, 
                        SettingRepository $settingRepository,                        
                        CustomFieldRepository $custom_fieldRepository,
                        QuoteRepository $quoteRepository
    ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => 'Edit',
            'action' => ['quotecustom/edit', ['id' => $this->quotecustom($currentRoute, $quotecustomRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->quotecustom($currentRoute, $quotecustomRepository)),
            'head'=>$head,
            's'=>$settingRepository,
            'custom_fields'=>$custom_fieldRepository->findAllPreloaded(),
            'quotes'=>$quoteRepository->findAllPreloaded()
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new QuoteCustomForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->quotecustomService->saveQuoteCustom($this->quotecustom($currentRoute, $quotecustomRepository), $form);
                return $this->webService->getRedirectResponse('quotecustom/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function delete(SessionInterface $session, CurrentRoute $currentRoute, QuoteCustomRepository $quotecustomRepository 
    ): Response {
        $this->rbac($session);
        try {
            $this->quotecustomService->deleteQuoteCustom($this->quotecustom($currentRoute, $quotecustomRepository));               
            $this->flash($session, 'info', 'Deleted.');
            return $this->webService->getRedirectResponse('quotecustom/index'); 
	} catch (Exception $e) {
            //unset($e);
            $this->flash($session, 'danger', $e);
            return $this->webService->getRedirectResponse('quotecustom/index'); 
        }
    }
    
    public function view(SessionInterface $session, CurrentRoute $currentRoute, QuoteCustomRepository $quotecustomRepository,
        SettingRepository $settingRepository,
        ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('view'),
            'action' => ['quotecustom/view', ['id' => $this->quotecustom($currentRoute, $quotecustomRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->quotecustom($currentRoute, $quotecustomRepository)),
            's'=>$settingRepository,             
            'quotecustom'=>$quotecustomRepository->repoQuoteCustomquery($this->quotecustom($currentRoute, $quotecustomRepository)->getId()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }
        
    private function rbac(SessionInterface $session) 
    {
        $canEdit = $this->userService->hasPermission('editQuoteCustom');
        if (!$canEdit){
            $this->flash($session,'warning', $this->translator->translate('invoice.permission'));
            return $this->webService->getRedirectResponse('quotecustom/index');
        }
        return $canEdit;
    }
    
    private function quotecustom(CurrentRoute $currentRoute, QuoteCustomRepository $quotecustomRepository) 
    {
        $id = $currentRoute->getArgument('id');       
        $quotecustom = $quotecustomRepository->repoQuoteCustomquery($id);
        if ($quotecustom === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $quotecustom;
    }
    
    private function quotecustoms(QuoteCustomRepository $quotecustomRepository) 
    {
        $quotecustoms = $quotecustomRepository->findAllPreloaded();        
        if ($quotecustoms === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $quotecustoms;
    }
    
    private function body($quotecustom) {
        $body = [
                
          'id'=>$quotecustom->getId(),
          'quote_id'=>$quotecustom->getQuote_id(),
          'custom_field_id'=>$quotecustom->getCustom_field_id(),
          'value'=>$quotecustom->getValue()
                ];
        return $body;
    }
    
    private function flash(SessionInterface $session, $level, $message){
        $flash = new Flash($session);
        $flash->set($level, $message); 
        return $flash;
    }
}