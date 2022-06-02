<?php

declare(strict_types=1); 

namespace App\Invoice\Sumex;

use App\Invoice\Entity\Sumex;
use App\Invoice\Sumex\SumexService;
use App\Invoice\Sumex\SumexForm;
use App\Invoice\Sumex\SumexRepository;
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
use Yiisoft\Yii\View\ViewRenderer;
use Yiisoft\Validator\ValidatorInterface;

final class SumexController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService;
    private SumexService $sumexService;
    private TranslatorInterface $translator;
    
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UserService $userService,
        SumexService $sumexService,
        TranslatorInterface $translator
    )    
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/sumex')
                                           ->withLayout(dirname(dirname(__DIR__)).'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->userService = $userService;
        $this->sumexService = $sumexService;
        $this->translator = $translator;
    }
    
    public function index(SessionInterface $session, SumexRepository $sumexRepository, SettingRepository $settingRepository, Request $request, SumexService $service): Response
    {
         $canEdit = $this->rbac($session);
         $flash = $this->flash($session, '','');
         $parameters = [
      
          's'=>$settingRepository,
          'canEdit' => $canEdit,
          'sumexs' => $this->sumexs($sumexRepository),
          'flash'=> $flash
         ];

        if ($this->isAjaxRequest($request)) {
            return $this->viewRenderer->renderPartial('_sumexs', ['data' => $paginator]);
        }
        
        return $this->viewRenderer->render('index', $parameters);
    }
    
    private function isAjaxRequest(Request $request): bool
    {
        return $request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
    }
    
    public function add(ViewRenderer $head,SessionInterface $session, Request $request, 
                        ValidatorInterface $validator,
                        SettingRepository $settingRepository
    )
    {
        $this->rbac($session);
        $parameters = [
            'title' => 'Add',
            'action' => ['sumex/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$settingRepository,
            'head'=>$head,
            
        ];
        
        if ($request->getMethod() === Method::POST) {
            
            $form = new SumexForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->sumexService->saveSumex(new Sumex(),$form);
                return $this->webService->getRedirectResponse('sumex/index');
            }
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function edit(ViewRenderer $head, SessionInterface $session, Request $request, CurrentRoute $currentRoute,
                        ValidatorInterface $validator,
                        SumexRepository $sumexRepository, 
                        SettingRepository $settingRepository
    ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => 'Edit',
            'action' => ['sumex/edit', ['id' => $this->sumex($currentRoute, $sumexRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->sumex($currentRoute, $sumexRepository)),
            's'=>$settingRepository,
            'head'=>$head,
            
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new SumexForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->sumexService->saveSumex($this->sumex($currentRoute, $sumexRepository), $form);
                return $this->webService->getRedirectResponse('sumex/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function delete(SessionInterface $session, CurrentRoute $currentRoute, SumexRepository $sumexRepository 
    ): Response {
        $this->rbac($session);
        $this->sumexService->deleteSumex($this->sumex($currentRoute, $sumexRepository));               
        return $this->webService->getRedirectResponse('sumex/index');        
    }
    
    public function view(SessionInterface $session, CurrentRoute $currentRoute, SumexRepository $sumexRepository,
        SettingRepository $settingRepository
        ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('view'),
            'action' => ['sumex/edit', ['id' => $this->sumex($currentRoute, $sumexRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->sumex($currentRoute, $sumexRepository)),
            's'=>$settingRepository,             
            'sumex'=>$sumexRepository->repoSumexquery($this->sumex($currentRoute, $sumexRepository)->getId()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }
    
    private function rbac(SessionInterface $session) 
    {
        $canEdit = $this->userService->hasPermission('editSumex');
        if (!$canEdit){
            $this->flash($session,'warning', $this->translator->translate('invoice.permission'));
            return $this->webService->getRedirectResponse('sumex/index');
        }
        return $canEdit;
    }
    
    private function sumex(CurrenRoute $currentRoute, SumexRepository $sumexRepository) 
    {
        $id = $currentRoute->getArgument('id');       
        $sumex = $sumexRepository->repoSumexquery($id);
        if ($sumex === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $sumex;
    }
    
    private function sumexs(SumexRepository $sumexRepository) 
    {
        $sumexs = $sumexRepository->findAllPreloaded();        
        if ($sumexs === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $sumexs;
    }
    
    private function body($sumex) {
        $body = [
          'invoice'=>$sumex->getInvoice(),
          'reason'=>$sumex->getReason(),
          'diagnosis'=>$sumex->getDiagnosis(),
          'observations'=>$sumex->getObservations(),
          'treatmentstart'=>$sumex->getTreatmentstart(),
          'treatmentend'=>$sumex->getTreatmentend(),
          'casedate'=>$sumex->getCasedate(),
          'casenumber'=>$sumex->getCasenumber()
        ];
        return $body;
    }
    
    private function flash(SessionInterface $session, $level, $message){
        $flash = new Flash($session);
        $flash->set($level, $message); 
        return $flash;
    }
}