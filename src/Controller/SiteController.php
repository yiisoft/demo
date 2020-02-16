<?php

namespace App\Controller;

use App\Controller;
use Psr\Http\Message\ResponseInterface;

class SiteController extends Controller
{
    protected function getId(): string
    {
        return 'site';
    }

    public function index(): ResponseInterface
    {
        return $this->render('index');
    }
}
