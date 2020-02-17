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
