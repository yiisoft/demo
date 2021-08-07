<?php

declare(strict_types=1); 

namespace App\Invoice\Merchant;

use App\Invoice\Entity\Merchant;
use App\Invoice\Merchant\MerchantService;
use App\Invoice\Merchant\MerchantRepository;
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

final class MerchantController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService;
    private MerchantService $merchantService;
    
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UserService $userService,
        MerchantService $merchantService
    )    
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/merchant')
                                           ->withLayout(dirname(dirname(__DIR__)).'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->userService = $userService;
        $this->merchantService = $merchantService;
    }
    
    public function index(SessionInterface $session, MerchantRepository $merchantRepository, SettingRepository $settingRepository, Request $request, MerchantService $service): Response
    {
       
         $canEdit = $this->rbac($session);
         $flash = $this->flash($session, 'dummy' , 'Flash message!.');
         $parameters = [
      
          's'=>$settingRepository,
          'canEdit' => $canEdit,
          'merchants' => $this->merchants($merchantRepository),
          'flash'=> $flash
         ];

        if ($this->isAjaxRequest($request)) {
            return $this->viewRenderer->renderPartial('_merchants', ['data' => $paginator]);
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
            'action' => ['merchant/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$settingRepository,
            'head'=>$head,
            
            'invs'=>$invRepository->findAllPreloaded(),
        ];
        
        if ($request->getMethod() === Method::POST) {
            
            $form = new MerchantForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->merchantService->saveMerchant(new Merchant(),$form);
                return $this->webService->getRedirectResponse('merchant/index');
            }
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function edit(ViewRenderer $head, SessionInterface $session, Request $request, 
                        ValidatorInterface $validator,
                        MerchantRepository $merchantRepository, 
                        SettingRepository $settingRepository,                        
                        InvRepository $invRepository
    ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => 'Edit',
            'action' => ['merchant/edit', ['id' => $this->merchant($request, $merchantRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->merchant($request, $merchantRepository)),
            'head'=>$head,
            's'=>$settingRepository,
            'invs'=>$invRepository->findAllPreloaded()
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new MerchantForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->merchantService->saveMerchant($this->merchant($request,$merchantRepository), $form);
                return $this->webService->getRedirectResponse('merchant/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function delete(SessionInterface $session,Request $request,MerchantRepository $merchantRepository 
    ): Response {
        $this->rbac($session);
        $this->flash($session, 'danger','This record has been deleted');
        $this->merchantService->deleteMerchant($this->merchant($request,$merchantRepository));               
        return $this->webService->getRedirectResponse('merchant/index');        
    }
    
    public function view(SessionInterface $session,Request $request,MerchantRepository $merchantRepository,
        SettingRepository $settingRepository
        ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('view'),
            'action' => ['merchant/edit', ['id' => $this->merchant($request, $merchantRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->merchant($request, $merchantRepository)),
            's'=>$settingRepository,             
            'merchant'=>$merchantRepository->repoMerchantquery($this->merchant($request, $merchantRepository)->getId()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }
    
    private function rbac(SessionInterface $session) 
    {
        $canEdit = $this->userService->hasPermission('editMerchant');
        if (!$canEdit){
            $this->flash($session,'warning', 'You do not have the required permission.');
            return $this->webService->getRedirectResponse('merchant/index');
        }
        return $canEdit;
    }
    
    private function merchant(Request $request,MerchantRepository $merchantRepository) 
    {
        $id = $request->getAttribute('id');       
        $merchant = $merchantRepository->repoMerchantquery($id);
        if ($merchant === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $merchant;
    }
    
    private function merchants(MerchantRepository $merchantRepository) 
    {
        $merchants = $merchantRepository->findAllPreloaded();        
        if ($merchants === null) {
            return $this->webService->getNotFoundResponse();
        };
        return $merchants;
    }
    
    private function body($merchant) {
        $body = [
          'inv_id'=>$merchant->getInv_id(),
          'successful'=>$merchant->getSuccessful(),
          'date'=>$merchant->getDate(),
          'driver'=>$merchant->getDriver(),
          'response'=>$merchant->getResponse(),
          'reference'=>$merchant->getReference()
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