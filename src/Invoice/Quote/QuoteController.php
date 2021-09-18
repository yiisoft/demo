<?php

declare(strict_types=1); 

namespace App\Invoice\Quote;

use App\Invoice\Entity\Quote;
use App\Invoice\Quote\QuoteService;
use App\Invoice\Quote\QuoteRepository;
use \Exception;
use App\Invoice\Setting\SettingRepository;
use App\Invoice\Inv\InvRepository;
use App\Invoice\Client\ClientRepository;
use App\Invoice\Group\GroupRepository;
use App\User\UserRepository;
use App\User\UserService;
use Yiisoft\Validator\ValidatorInterface;
use App\Service\WebControllerService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Http\Method;
use Yiisoft\Yii\View\ViewRenderer;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Session\Flash\Flash;
use Yiisoft\Data\Paginator\OffsetPaginator;

final class QuoteController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService;
    private QuoteService $quoteService;
    private const QUOTES_PER_PAGE = 5;
    
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UserService $userService,
        QuoteService $quoteService
    )    
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/quote')
                                           ->withLayout(dirname(dirname(__DIR__)) .'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->userService = $userService;
        $this->quoteService = $quoteService;
    }
    
    public function index_old(SessionInterface $session, QuoteRepository $quoteRepository, SettingRepository $settingRepository, Request $request, QuoteService $service): Response
    {      
         $canEdit = $this->rbac($session);
         $flash = $this->flash($session, 'dummy' , 'Flash message!.');
         $parameters = [
      
          's'=>$settingRepository,
          'canEdit' => $canEdit,
          'quotes' => $this->quotes($quoteRepository),
          'flash'=> $flash
         ];

        if ($this->isAjaxRequest($request)) {
            return $this->viewRenderer->renderPartial('_quotes', ['data' => $paginator]);
        }
        
        return $this->viewRenderer->render('index_old', $parameters);
    }
    
    public function index(SessionInterface $session, QuoteRepository $quoteRepository, SettingRepository $settingRepository, Request $request, QuoteService $service): Response
    {
            
        $pageNum = (int)$request->getAttribute('page', 1);
        $paginator = (new OffsetPaginator($this->quotes($quoteRepository)))
        ->withPageSize(self::QUOTES_PER_PAGE)
        ->withCurrentPage($pageNum);
       
        $canEdit = $this->rbac($session);
        $flash = $this->flash($session, 'dummy' , 'Flash message!.');
        $parameters = [
              'paginator' => $paginator,
              's'=>$settingRepository,
              'canEdit' => $canEdit,
              'quotes' => $this->quotes($quoteRepository),
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
                        InvRepository $invRepository,
                        ClientRepository $clientRepository,
                        GroupRepository $groupRepository,
                        UserRepository $userRepository
    )
    {
        $this->rbac($session);
        $parameters = [
            'title' => 'Add',
            'action' => ['quote/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$settingRepository,
            'head'=>$head,
            
            'invs'=>$invRepository->findAllPreloaded(),
            'clients'=>$clientRepository->findAllPreloaded(),
            'groups'=>$groupRepository->findAllPreloaded(),
            'users'=>$userRepository->findAll(),
        ];
        
        if ($request->getMethod() === Method::POST) {
            
            $form = new QuoteForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->quoteService->saveQuote($this->userService->getUser(),new Quote(),$form,$settingRepository);
                return $this->webService->getRedirectResponse('quote/index');
            }
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function edit(ViewRenderer $head, SessionInterface $session, Request $request, 
                        ValidatorInterface $validator,
                        QuoteRepository $quoteRepository, 
                        SettingRepository $settingRepository,                        
                        InvRepository $invRepository,
                        ClientRepository $clientRepository,
                        GroupRepository $groupRepository,
                        UserRepository $userRepository
    ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => 'Edit',
            'action' => ['quote/edit', ['id' => $this->quote($request, $quoteRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->quote($request, $quoteRepository)),
            'head'=>$head,
            's'=>$settingRepository,
            'invs'=>$invRepository->findAllPreloaded(),
            'clients'=>$clientRepository->findAllPreloaded(),
            'groups'=>$groupRepository->findAllPreloaded(),
            'users'=>$userRepository->findAll()
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new QuoteForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
               $this->quoteService->saveQuote($this->userService->getUser(),$this->quote($request, $quoteRepository),$form,$settingRepository);
                return $this->webService->getRedirectResponse('quote/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function delete(SessionInterface $session,Request $request,QuoteRepository $quoteRepository 
    ): Response {
        $this->rbac($session);
        try {
            $this->quoteService->deleteQuote($this->quote($request,$quoteRepository)); 
            $this->flash($session, 'info', 'Deleted.');
            return $this->webService->getRedirectResponse('quote/index'); 
	} catch (Exception $e) {
            unset($e);
            $this->flash($session, 'danger', 'Cannot delete.');
            return $this->webService->getRedirectResponse('quote/index'); 
        }
    }
    
    public function view(SessionInterface $session,Request $request,QuoteRepository $quoteRepository,
        SettingRepository $settingRepository
        ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('view'),
            'action' => ['quote/view', ['id' => $this->quote($request, $quoteRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->quote($request, $quoteRepository)),
            's'=>$settingRepository,             
            'quote'=>$quoteRepository->repoQuotequery($this->quote($request, $quoteRepository)->getId()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }
        
    private function rbac(SessionInterface $session) 
    {
        $canEdit = $this->userService->hasPermission('editQuote');
        if (!$canEdit){
            $this->flash($session,'warning', 'You do not have the required permission.');
            return $this->webService->getRedirectResponse('quote/index');
        }
        return $canEdit;
    }
    
    private function quote(Request $request,QuoteRepository $quoteRepository) 
    {
        $id = $request->getAttribute('id');       
        $quote = $quoteRepository->repoQuotequery($id);
        if ($quote === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $quote;
    }
    
    private function quotes(QuoteRepository $quoteRepository) 
    {
        $quotes = $quoteRepository->findAllPreloaded();        
        if ($quotes === null) {
            return $this->webService->getNotFoundResponse();
        };
        return $quotes;
    }
    
    private function body($quote) {
        $body = [
          'id'=>$quote->getId(),
          'inv_id'=>$quote->getInv_id(),
          'user_id'=>$quote->getUser_id(),
          'client_id'=>$quote->getClient_id(),
          'group_id'=>$quote->getGroup_id(),
          'status_id'=>$quote->getStatus_id(),
          'date_created'=>$quote->getDate_created(),
          'date_modified'=>$quote->getDate_modified(),
          'date_expires'=>$quote->getDate_expires(),
          'number'=>$quote->getNumber(),
          'discount_amount'=>$quote->getDiscount_amount(),
          'discount_percent'=>$quote->getDiscount_percent(),
          'url_key'=>$quote->getUrl_key(),
          'password'=>$quote->getPassword(),
          'notes'=>$quote->getNotes()
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