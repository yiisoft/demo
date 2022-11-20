<?php

declare(strict_types=1);

namespace App\Auth;

use App\Exception\UnauthorisedException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class AuthRequestErrorHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        throw new UnauthorisedException();
    }
}
