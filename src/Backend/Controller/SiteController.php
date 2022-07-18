<?php

declare(strict_types=1);

namespace App\Backend\Controller;

use Psr\Http\Message\ResponseInterface;
use Yiisoft\Yii\View\ViewRenderer;

final class SiteController
{
    private ViewRenderer $viewRenderer;

    public function __construct(ViewRenderer $viewRenderer)
    {
        $this->viewRenderer = $viewRenderer
            ->withController($this)
            ->withViewPath('@resources/backend/views');
    }

    public function index(): ResponseInterface
    {
        return $this->viewRenderer->render('index');
    }
}
