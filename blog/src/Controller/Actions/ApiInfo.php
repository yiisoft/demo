<?php

declare(strict_types=1);

namespace App\Controller\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yiisoft\DataResponse\DataResponseFactoryInterface;
use OpenApi\Attributes as OA;

#[OA\Info(version: '2.0', title: 'Yii demo API')]
final class ApiInfo implements MiddlewareInterface
{
    private DataResponseFactoryInterface $responseFactory;

    public function __construct(DataResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    #[OA\Get(
        path: '/api/info/v2',
        description: '',
        summary: 'Returns info about the API',
        responses: [
            new OA\Response(
                response:'200',
                description:'Success',
                content: new OA\JsonContent(
                    allOf: [
                        new OA\Schema(ref: '#/components/schemas/Response'),
                        new OA\Schema(properties: [
                            new OA\Property(property: 'data', properties: [
                                new OA\Property(property: 'version', type: 'string', example: '2.0'),
                                new OA\Property(property: 'author', type: 'string', example: 'yiisoft'),
                            ], type: 'object'),
                        ]),
                    ]
                ),
            ),
        ]
    )]
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->responseFactory->createResponse(['version' => '2.0', 'author' => 'yiisoft']);
    }
}
