<?php

declare(strict_types=1);

namespace App\Controller;

use App\Middleware\ApiDataWrapper;
use Psr\Http\Message\ResponseInterface;
use Yiisoft\DataResponse\Middleware\FormatDataResponseAsJson;
use Yiisoft\DataResponse\Middleware\FormatDataResponseAsXml;
use Yiisoft\Router\Route;
use Yiisoft\Yii\View\Renderer\ViewRenderer;

final class SiteController
{
    public function __construct(private ViewRenderer $viewRenderer)
    {
        $this->viewRenderer = $viewRenderer->withController($this);
    }

    #[Route(
        methods: ['GET'],
        pattern: '/',
        name: 'site/index',
    )]
    public function index(): ResponseInterface
    {
        return $this->viewRenderer->render('index');
    }

    #[Route(
        methods: ['GET'],
        pattern: '/json',
        name: 'site/index.json',
        middlewares: [
            FormatDataResponseAsJson::class,
            ApiDataWrapper::class,
        ],
    )]
    public function json(): ResponseInterface
    {
        return $this->viewRenderer->render('index');
    }

    #[Route(
        methods: ['GET'],
        pattern: '/xml',
        name: 'site/index.xml',
        middlewares: [
            FormatDataResponseAsXml::class,
            ApiDataWrapper::class,
        ],
    )]
    public function xml(): ResponseInterface
    {
        return $this->viewRenderer->render('index');
    }
}
