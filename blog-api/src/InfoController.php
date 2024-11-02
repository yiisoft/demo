<?php

declare(strict_types=1);

namespace App;

use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Yiisoft\DataResponse\DataResponseFactoryInterface;

#[OA\Info(version: '1.0', title: 'Yii API application')]
class InfoController
{
    public function __construct(private VersionProvider $versionProvider)
    {
    }

    #[OA\Get(
        path: '/',
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
                                new OA\Property(property: 'version', type: 'string', example: '3.0'),
                                new OA\Property(property: 'author', type: 'string', example: 'yiisoft'),
                            ], type: 'object'),
                        ]),
                    ]
                ),
            ),
        ]
    )]
    public function index(DataResponseFactoryInterface $responseFactory): ResponseInterface
    {
        return $responseFactory->createResponse(['version' => $this->versionProvider->version, 'author' => 'yiisoft']);
    }
}
