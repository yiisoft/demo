<?php

declare(strict_types=1);

namespace App\Auth;

use App\User\UserRequest;
use App\User\UserService;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Yiisoft\DataResponse\DataResponseFactoryInterface as ResponseFactory;

#[OA\Tag(name: 'auth', description: 'Authentication')]
#[OA\SecurityScheme(securityScheme: 'ApiKey', type: 'apiKey', name: 'X-Api-Key', in: 'header')]
final class AuthController
{
    private ResponseFactory $responseFactory;
    private UserService $userService;

    public function __construct(
        ResponseFactory $responseFactory,
        UserService $userService
    ) {
        $this->responseFactory = $responseFactory;
        $this->userService = $userService;
    }

    #[OA\Post(
        path: '/auth/',
        description: '',
        summary: 'Authenticate by params',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
            allOf: [
                new OA\Schema(ref: '#/components/schemas/AuthRequest'),
            ]
        )),
        tags: ['auth'],
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
                                    new OA\Property(property: 'token', type: 'string', example: 'uap4X5Bd7078lxIFvxAflcGAa5D95iSSZkNjg3XFrE2EBRBlbj'),
                                ],
                                type: 'object'
                            ),
                        ]),
                    ]
                )
            ),
            new OA\Response(
                response: '400',
                description: 'Bad request',
                content: new OA\JsonContent(ref:  '#/components/schemas/BadResponse')
            ),
        ]
    )]
    public function login(AuthRequest $request): ResponseInterface
    {
        return $this->responseFactory->createResponse(
            [
                'token' => $this->userService
                    ->login(
                        $request->getLogin(),
                        $request->getPassword()
                    )
                    ->getToken(),
            ]
        );
    }

    #[OA\Post(
        path: '/logout/',
        description: '',
        summary: 'Logout',
        security: [new OA\SecurityScheme(ref: '#/components/securitySchemes/ApiKey')],
        tags: ['auth'],
        responses: [
            new OA\Response(
                response: '200',
                description: 'Success',
                content: new OA\JsonContent(ref:  '#/components/schemas/Response')
            ),
            new OA\Response(
                response: '400',
                description: 'Bad request',
                content: new OA\JsonContent(ref:  '#/components/schemas/BadResponse')
            ),
        ]
    )]
    public function logout(UserRequest $request): ResponseInterface
    {
        $this->userService->logout($request->getUser());

        return $this->responseFactory->createResponse();
    }
}
