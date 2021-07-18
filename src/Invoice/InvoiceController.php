<?php

declare(strict_types=1);

namespace App\Invoice;

use Psr\Http\Message\ResponseInterface as Response;
use App\Invoice\Setting\SettingRepository;
use Yiisoft\User\CurrentUser;
use Yiisoft\Yii\View\ViewRenderer;
use App\Service\WebControllerService;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Session\Flash\Flash;
use App\User\UserService;
use Spiral\Database\DatabaseManager;

final class InvoiceController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService; 
        
    public function __construct(ViewRenderer $viewRenderer, WebControllerService $webService, UserService $userService)
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice')
                                           ->withLayout(dirname(dirname(__DIR__)).'/src/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->userService = $userService;
        
    }

    public function index(SessionInterface $session, CurrentUser $currentUser, DatabaseManager $dbal
    ): Response {
        $canEdit = $this->rbac($session);
        $data = [
            'isGuest' => $currentUser->isGuest(),
            'canEdit' => $canEdit, 
            'tables'=>$dbal->database('default')->getTables()
        ];
        return $this->viewRenderer->render('index', $data);
    }
    
    private function rbac(SessionInterface $session) {
        $canEdit = $this->userService->hasPermission('editGenerator');
        if (!$canEdit){
            $this->flash($session,'warning', 'You do not have the required permission.');
            return $this->webService->getRedirectResponse('invoice/index');
        }
        return $canEdit;
    }
    
    private function flash(SessionInterface $session, $level, $message){
        $flash = new Flash($session);
        $flash->set($level, $message); 
        return $flash;
    }
}


