<?php
declare(strict_types=1); 

namespace App\Invoice\Guest;

use App\Service\WebControllerService;
use App\User\UserService;

use App\Invoice\Quote\QuoteRepository as QR;
use App\Invoice\Inv\InvRepository as IR;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Session\Flash\Flash;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\View\ViewRenderer;

final class GuestController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService;
    
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UserService $userService,
    )    
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/group')
                                           ->withLayout(dirname(dirname(__DIR__) ).'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->userService = $userService;
    }
    
    public function index(QR $qR, IR $iR): Response
    {    
        
    }
    
    private function flash(SessionInterface $session, $level, $message){
        $flash = new Flash($session);
        $flash->set($level, $message); 
        return $flash;
    }
}