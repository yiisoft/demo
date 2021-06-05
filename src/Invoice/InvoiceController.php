<?php

declare(strict_types=1);

namespace App\Invoice;

use Psr\Http\Message\ResponseInterface as Response;
use App\Invoice\Setting\SettingRepository;
use Yiisoft\User\CurrentUser;
use Yiisoft\Yii\View\ViewRenderer;

final class InvoiceController
{
    private ViewRenderer $viewRenderer;

    public function __construct(ViewRenderer $viewRenderer)
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice')
                                           ->withLayout(dirname(dirname(__DIR__)).'/src/Invoice/Layout/main.php');                                            
    }

    public function index(
        CurrentUser $currentUser, SettingRepository $settingRepository    
    ): Response {
        $client = $settingRepository->trans('client');
        $data = [
            'isGuest' => $currentUser->isGuest(),
            'client'=>$client
        ];
        return $this->viewRenderer->render('index', $data);
    }
}
