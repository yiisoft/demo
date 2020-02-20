<?php

namespace App\LazyRendering\Http;

use Psr\Http\Message\ServerRequestInterface;
use Throwable;

final class HttpNotFoundException extends \Exception
{
    private ServerRequestInterface $request;

    public function __construct(ServerRequestInterface $request, $message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct(
            $message,
            $code,
            $previous
        );
        $this->request = $request;
    }

    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }
}
