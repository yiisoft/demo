<?php

declare(strict_types=1);

namespace App\User;

use App\Exception\NotFoundException;
use App\RestControllerTrait;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Yiisoft\DataResponse\DataResponseFactoryInterface;
use Yiisoft\Router\HydratorAttribute\RouteArgument;

#[OA\Tag(name: 'user', description: 'User')]
final class UserController
{
    use RestControllerTrait;

    private DataResponseFactoryInterface $responseFactory;
    private UserRepository $userRepository;
    private UserFormatter $userFormatter;

    public function __construct(
        DataResponseFactoryInterface $responseFactory,
        UserRepository $userRepository,
        UserFormatter $userFormatter
    ) {
        $this->responseFactory = $responseFactory;
        $this->userRepository = $userRepository;
        $this->userFormatter = $userFormatter;
    }

    #[OA\Get(
        path: '/users/',
        description: '',
        summary: 'Returns paginated users',
        security: [new OA\SecurityScheme(ref: '#/components/securitySchemes/ApiKey')],
        tags: ['user'],
        responses: [
            new OA\Response(
                response: '200',
                description: 'Success',
                content: new OA\JsonContent(
                    allOf: [
                        new OA\Schema(ref: '#/components/schemas/Response'),
                        new OA\Schema(properties: [
                            new OA\Property(
                                property: 'data',
                                properties: [
                                    new OA\Property(
                                        property: 'user',
                                        type: 'array',
                                        items: new OA\Items(ref: '#/components/schemas/User')
                                    ),
                                ],
                                type: 'object'
                            ),
                        ]),
                    ]
                )
            ),
        ]
    )]
    public function list(): ResponseInterface
    {
        $dataReader = $this->userRepository->findAllOrderByLogin();
        $result = [];
        foreach ($dataReader->read() as $user) {
            $result[] = $this->userFormatter->format($user);
        }

        return $this->responseFactory->createResponse(
            [
                'users' => $result,
            ]
        );
    }

    #[OA\Get(
        path: '/users/{id}',
        description: '',
        summary: 'Returns a user with a given ID',
        security: [new OA\SecurityScheme(ref: '#/components/securitySchemes/ApiKey')],
        tags: ['user'],
        parameters: [
            new OA\Parameter(parameter: 'id', name: 'id', in: 'path', example: 2),
        ],
        responses: [
            new OA\Response(
                response: '200',
                description: 'Success',
                content: new OA\JsonContent(
                    allOf: [
                        new OA\Schema(ref: '#/components/schemas/Response'),
                        new OA\Schema(properties: [
                            new OA\Property(
                                property: 'data',
                                properties: [
                                    new OA\Property(property: 'user', ref: '#/components/schemas/User', type: 'object'),
                                ],
                                type: 'object'
                            ),
                        ]),
                    ]
                )
            ),
        ]
    )]
    public function get(#[RouteArgument('id')] int $id): ResponseInterface
    {
        /**
         * @var User $user
         */
        $user = $this->userRepository->findByPK($id);
        if ($user === null) {
            throw new NotFoundException();
        }

        return $this->responseFactory->createResponse(
            [
                'user' => $this->userFormatter->format($user),
            ]
        );
    }
}
