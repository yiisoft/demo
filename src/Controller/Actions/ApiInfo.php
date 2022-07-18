<?php

declare(strict_types=1);

namespace App\Controller\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yiisoft\DataResponse\DataResponseFactoryInterface;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(title="Yii demo API", version="2.0")
 */
final class ApiInfo implements MiddlewareInterface
{
    private DataResponseFactoryInterface $responseFactory;

    public function __construct(DataResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    /**
     * @OA\Get(
     *     path="/api/info/v2",
     *     @OA\Response(response="200", description="Get api version")
     * )
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->responseFactory->createResponse(['version' => '2.0', 'author' => 'yiisoft']);
    }
}
