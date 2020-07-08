<?php

namespace App\Controller;

use App\Controller;
use Psr\Http\Message\ResponseInterface;

class SiteController extends Controller
{
    public function index(): ResponseInterface
    {
        return $this->render('index');
    }
}
