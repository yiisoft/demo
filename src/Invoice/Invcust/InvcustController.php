<?php

declare(strict_types=1); 

namespace App\Invoice\Invcust;

use App\Invoice\Entity\Invcust;
use App\Invoice\Invcust\InvcustService;
use App\Invoice\Invcust\InvcustRepository;
use App\Invoice\Setting\SettingRepository;
use App\Invoice\Inv\InvRepository;
use App\User\UserService;
use Yiisoft\Validator\ValidatorInterface;
use App\Service\WebControllerService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Http\Method;
use Yiisoft\Yii\View\ViewRenderer;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Session\Flash\Flash;

final class InvcustController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService;
    private InvcustService $invcustService;
    
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UserService $userService,
        InvcustService $invcustService
    )    
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/invcust')
                                           ->withLayout(dirname(dirname(__DIR__)) .'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->userService = $userService;
        $this->invcustService = $invcustService;
    }
    
    public function index(SessionInterface $session, InvcustRepository $invcustRepository, SettingRepository $settingRepository, Request $request, InvcustService $service): Response
    {
       
         $canEdit = $this->rbac($session);
         $flash = $this->flash($session, 'dummy' , 'Flash message!.');
         $parameters = [
      
          's'=>$settingRepository,
          'canEdit' => $canEdit,
          'invcusts' => $this->invcusts($invcustRepository),
          'flash'=> $flash
         ];

        if ($this->isAjaxRequest($request)) {
            return $this->viewRenderer->renderPartial('_invcusts', ['data' => $paginator]);
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
                        InvRepository $invRepository
    )
    {
        $this->rbac($session);
        $parameters = [
            'title' => 'Add',
            'action' => ['invcust/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$settingRepository,
            'head'=>$head,
            
            'invs'=>$invRepository->findAllPreloaded(),
        ];
        
        if ($request->getMethod() === Method::POST) {
            
            $form = new InvcustForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->invcustService->saveInvcust(new Invcust(),$form);
                return $this->webService->getRedirectResponse('invcust/index');
            }
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function edit(ViewRenderer $head, SessionInterface $session, Request $request, 
                        ValidatorInterface $validator,
                        InvcustRepository $invcustRepository, 
                        SettingRepository $settingRepository,                        
                        InvRepository $invRepository
    ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => 'Edit',
            'action' => ['invcust/edit', ['id' => $this->invcust($request, $invcustRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->invcust($request, $invcustRepository)),
            'head'=>$head,
            's'=>$settingRepository,
                        'invs'=>$invRepository->findAllPreloaded()
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new InvcustForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->invcustService->saveInvcust($this->invcust($request,$invcustRepository), $form);
                return $this->webService->getRedirectResponse('invcust/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function delete(SessionInterface $session,Request $request,InvcustRepository $invcustRepository 
    ): Response {
        $this->rbac($session);
        $this->flash($session, 'danger','This record has been deleted');
        $this->invcustService->deleteInvcust($this->invcust($request,$invcustRepository));               
        return $this->webService->getRedirectResponse('invcust/index');        
    }
    
    public function view(SessionInterface $session,Request $request,InvcustRepository $invcustRepository,
        SettingRepository $settingRepository,
        ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('view'),
            'action' => ['invcust/edit', ['id' => $this->invcust($request, $invcustRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->invcust($request, $invcustRepository)),
            's'=>$settingRepository,             
            'invcust'=>$invcustRepository->repoInvcustquery($this->invcust($request, $invcustRepository)->getId()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }
    
    private function rbac(SessionInterface $session) 
    {
        $canEdit = $this->userService->hasPermission('editInvcust');
        if (!$canEdit){
            $this->flash($session,'warning', 'You do not have the required permission.');
            return $this->webService->getRedirectResponse('invcust/index');
        }
        return $canEdit;
    }
    
    private function invcust(Request $request,InvcustRepository $invcustRepository) 
    {
        $id = $request->getAttribute('id');       
        $invcust = $invcustRepository->repoInvcustquery($id);
        if ($invcust === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $invcust;
    }
    
    private function invcusts(InvcustRepository $invcustRepository) 
    {
        $invcusts = $invcustRepository->findAllPreloaded();        
        if ($invcusts === null) {
            return $this->webService->getNotFoundResponse();
        };
        return $invcusts;
    }
    
    private function body($invcust) {
        $body = [
                
          'id'=>$invcust->getId(),
          'inv_id'=>$invcust->getInv_id(),
          'fieldid'=>$invcust->getFieldid(),
          'fieldvalue'=>$invcust->getFieldvalue()
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