<?php

namespace App\Controller;

use Psr\Http\Message\ResponseInterface;
use App\ViewRenderer;

class SiteController
{
    private ViewRenderer $viewRenderer;

    public function __construct(ViewRenderer $viewRenderer)
    {
        $this->viewRenderer = $viewRenderer->withController($this);
    }

    public function index(): ResponseInterface
    {
        return $this->viewRenderer->render('index');
    }
}
