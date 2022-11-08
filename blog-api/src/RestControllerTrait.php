<?php

declare(strict_types=1);

namespace App;

use App\Exception\MethodNotAllowedException;
use Psr\Http\Message\ResponseInterface;

trait RestControllerTrait
{
    public function delete(): ResponseInterface
    {
        throw new MethodNotAllowedException();
    }

    public function head(): ResponseInterface
    {
        throw new MethodNotAllowedException();
    }

    public function get(): ResponseInterface
    {
        throw new MethodNotAllowedException();
    }

    public function list(): ResponseInterface
    {
        throw new MethodNotAllowedException();
    }

    public function options(): ResponseInterface
    {
        throw new MethodNotAllowedException();
    }

    public function patch(): ResponseInterface
    {
        throw new MethodNotAllowedException();
    }

    public function post(): ResponseInterface
    {
        throw new MethodNotAllowedException();
    }

    public function put(): ResponseInterface
    {
        throw new MethodNotAllowedException();
    }
}
