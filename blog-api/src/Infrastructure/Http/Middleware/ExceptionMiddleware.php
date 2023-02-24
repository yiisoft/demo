<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Middleware;

use App\Application\Exception\ApplicationException;
use App\Infrastructure\Http\HttpExceptionMapper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yiisoft\DataResponse\DataResponseFactoryInterface;
use Yiisoft\Http\Status;
use Yiisoft\RequestModel\RequestValidationException;

final class ExceptionMiddleware implements MiddlewareInterface
{
    public function __construct(
        private DataResponseFactoryInterface $dataResponseFactory,
        private HttpExceptionMapper $httpExceptionMapper,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (ApplicationException $e) {
            $code = $this->httpExceptionMapper->getCode($e);

            return $this->dataResponseFactory->createResponse($e->getMessage(), $code);
        } catch (RequestValidationException $e) {
            return $this->dataResponseFactory->createResponse($e->getFirstError(), Status::BAD_REQUEST);
        }
    }
}
