<?php

namespace Yiisoft\Yii\Demo\Controllers;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SiteController
{
    private $responseFactory;

    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    public function index(): ResponseInterface
    {
        $response = $this->responseFactory->createResponse();
        $response->getBody()->write('You are at homepage.');
        return $response;
    }

    public function testParameter(ServerRequestInterface $request, RequestHandlerInterface $next): ResponseInterface
    {
        $id = $request->getAttribute('id');

        $response = $this->responseFactory->createResponse();
        $response->getBody()->write('You are at test with param ' . $id);
        return $response;
    }
}
