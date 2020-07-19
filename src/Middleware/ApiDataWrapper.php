<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yiisoft\DataResponse\DataResponse;

class ApiDataWrapper implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        if ($response instanceof DataResponse) {
            $data = $response->getData();
            if ($response->getStatusCode() !== 200) {
                if (is_string($data) && !empty($data)) {
                    $message = $data;
                } else {
                    $message = 'Unknown error';
                }
                return $response->withData([
                    'status' => 'failed',
                    'error' => ['message' => $message, 'status' => $response->getStatusCode()],
                ]);
            }
            return $response->withData(['status' => 'success', 'data' => $data]);
        }

        return $response;
    }
}
