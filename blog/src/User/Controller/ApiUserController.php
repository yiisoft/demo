<?php

declare(strict_types=1);

namespace App\User\Controller;

use App\User\User;
use App\User\UserRepository;
use Psr\Http\Message\ResponseInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\DataResponse\DataResponseFactoryInterface;
use Yiisoft\Router\CurrentRoute;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'user', description: 'User')]
final class ApiUserController
{
    public function __construct(private DataResponseFactoryInterface $responseFactory)
    {
    }

    #[OA\Get(
        path: '/api/user',
        description: '',
        summary: 'Get users list',
        tags: ['user'],
        responses: [
            new OA\Response(
                response: '200',
                description: 'Success',
                content: new OA\XmlContent(
                    xml: new OA\Xml(name: 'response'),
                    allOf: [
                        new OA\Schema(ref: '#/components/schemas/Response'),
                        new OA\Schema(properties: [
                            new OA\Property(
                                property: 'data',
                                type: 'array',
                                items: new OA\Items(properties: [
                                    new OA\Property(property: 'login', type: 'string', example: 'exampleLogin'),
                                    new OA\Property(property: 'created_at', type: 'string', example: '13.12.2020 00:04:20'),
                                ])
                            ),
                        ]),
                    ]
                )
            ),
        ]
    )]
    public function index(UserRepository $userRepository): ResponseInterface
    {
        $dataReader = $userRepository
            ->findAll()
            ->withSort(Sort::only(['login'])->withOrderString('login'));
        $users = $dataReader->read();

        $items = [];
        foreach ($users as $user) {
            $items[] = ['login' => $user->getLogin(), 'created_at' => $user
                ->getCreatedAt()
                ->format('H:i:s d.m.Y'), ];
        }

        return $this->responseFactory->createResponse($items);
    }

    #[OA\Get(
        path: '/api/user/{login}',
        description: '',
        summary: 'Get user info',
        tags: ['user'],
        parameters: [
            new OA\Parameter(parameter: 'login', name: 'Login', in: 'path'),
        ],
        responses: [
            new OA\Response(
                response: '200',
                description: 'Success',
                content: new OA\JsonContent(
                    allOf: [
                        new OA\Schema(ref: '#/components/schemas/Response'),
                        new OA\Schema(properties: [
                            new OA\Property(property: 'data', properties: [
                                new OA\Property(property: 'login', type: 'string', example: 'exampleLogin'),
                                new OA\Property(property: 'created_at', type: 'string', example: '13.12.2020 00:04:20'),
                            ], type: 'object'),
                        ]),
                    ]
                ),
            ),
        ]
    )]
    public function profile(UserRepository $userRepository, CurrentRoute $currentRoute): ResponseInterface
    {
        $login = $currentRoute->getArgument('login');

        /** @var User $user */
        $user = $userRepository->findByLogin($login);
        if ($user === null) {
            return $this->responseFactory->createResponse('Page not found', 404);
        }

        return $this->responseFactory->createResponse(
            ['login' => $user->getLogin(), 'created_at' => $user
                ->getCreatedAt()
                ->format('H:i:s d.m.Y'), ]
        );
    }
}
