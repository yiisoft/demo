<?php
namespace Yiisoft\Yii\Demo\Controllers;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\View\WebView;

class SiteController
{
    private $responseFactory;

    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    public function index(WebView $view): ResponseInterface
    {
        $response = $this->responseFactory->createResponse();

        $output = $view->render('index');

        $response->getBody()->write($output);
        return $response;
    }

    public function testParameter(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute('id');

        $response = $this->responseFactory->createResponse();
        $response->getBody()->write('You are at test with param ' . $id);
        return $response;
    }
}
