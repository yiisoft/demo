<?php

namespace App\Controller;

use App\Controller;
use Cycle\ORM\ORMInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CycleController extends Controller
{
    protected function getId(): string
    {
        return 'cycle';
    }

    public function testConnection(ServerRequestInterface $request, ORMInterface $orm): ResponseInterface
    {
        $response = $this->responseFactory->createResponse();
        $response->getBody()->write('bla');

        return $response;
    }
}
