<?php

namespace App\Controller;

use App\Controller;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class SiteController extends Controller
{
    protected function getId(): string
    {
        return 'site';
    }

    public function index(): ResponseInterface
    {
        $response = $this->responseFactory->createResponse();

        $output = $this->render('index');

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

    public function stream(): ResponseInterface
    {
        for ($j = ob_get_level(), $i = 0; $i < $j; ++$i) {
            ob_end_flush();
        }
        $generator = function (int $i = 100) {
            do {
                usleep(100000);
                yield date("r\n");
            } while(--$i);
        };

        $stream = new \App\GeneratorStream($generator(100));
        return $this->responseFactory->createResponse()->withBody($stream);
    }
}
