<?php

declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\ResponseInterface;
use Yiisoft\Router\Attribute\Get;
use Yiisoft\Router\Group;
use Yiisoft\Yii\View\ViewRenderer;

#[Group('/{_language}')]
final class SiteController
{
    public function __construct(private ViewRenderer $viewRenderer)
    {
        $this->viewRenderer = $viewRenderer->withController($this);
    }

    #[Get('/', 'site/index')]
    public function index(): ResponseInterface
    {
        return $this->viewRenderer->render('index');
    }
}
