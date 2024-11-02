<?php

declare(strict_types=1);

namespace App\Blog;

use App\Formatter\PaginatorFormatter;
use App\User\UserRequest;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface as Response;
use Yiisoft\DataResponse\DataResponseFactoryInterface;
use Yiisoft\Input\Http\Attribute\Parameter\Query;
use Yiisoft\Router\HydratorAttribute\RouteArgument;

#[OA\Tag(name: 'blog', description: 'Blog')]
#[OA\Parameter(parameter: 'PageRequest', name: 'page', in: 'query', schema: new OA\Schema(type: 'int', example: '2'))]
final class BlogController
{
    private DataResponseFactoryInterface $responseFactory;
    private PostRepository $postRepository;
    private PostFormatter $postFormatter;
    private PostBuilder $postBuilder;
    private BlogService $blogService;

    public function __construct(
        PostRepository $postRepository,
        DataResponseFactoryInterface $responseFactory,
        PostFormatter $postFormatter,
        PostBuilder $postBuilder,
        BlogService $blogService
    ) {
        $this->postRepository = $postRepository;
        $this->responseFactory = $responseFactory;
        $this->postFormatter = $postFormatter;
        $this->postBuilder = $postBuilder;
        $this->blogService = $blogService;
    }

    #[OA\Get(
        path: '/blog/',
        description: '',
        summary: 'Returns paginated blog posts',
        tags: ['blog'],
        parameters: [
            new OA\Parameter(ref: '#/components/parameters/PageRequest'),
        ],
        responses: [
            new OA\Response(
                response:'200',
                description:'Success',
                content: new OA\JsonContent(
                    allOf: [
                        new OA\Schema(ref: '#/components/schemas/Response'),
                        new OA\Schema(properties: [
                            new OA\Property(
                                property: 'data',
                                properties: [
                                    new OA\Property(property: 'posts', type: 'array', items: new OA\Items(ref:'#/components/schemas/Post')),
                                    new OA\Property(property: 'paginator', ref: '#/components/schemas/Paginator', type: 'object'),
                                ],
                                type: 'object'
                            ),
                        ]),
                    ]
                ),
            ),
        ]
    )]
    public function index(PaginatorFormatter $paginatorFormatter, #[Query('page')] int $page = 1): Response
    {
        $paginator = $this->blogService->getPosts($page);
        $posts = [];
        foreach ($paginator->read() as $post) {
            $posts[] = $this->postFormatter->format($post);
        }

        return $this->responseFactory->createResponse(
            [
                'paginator' => $paginatorFormatter->format($paginator),
                'posts' => $posts,
            ]
        );
    }

    #[OA\Get(
        path: '/blog/{id}',
        description: '',
        summary: 'Returns a post with a given ID',
        tags: ['blog'],
        parameters: [
            new OA\Parameter(parameter: 'id', name: 'id', in: 'path', schema: new OA\Schema(type: 'int', example: '2')),
        ],
        responses: [
            new OA\Response(
                response:'200',
                description:'Success',
                content: new OA\JsonContent(
                    allOf: [
                        new OA\Schema(ref: '#/components/schemas/Response'),
                        new OA\Schema(properties: [
                            new OA\Property(
                                property: 'data',
                                properties: [
                                    new OA\Property(property: 'post', ref: '#/components/schemas/Post', type: 'object'),
                                ],
                                type: 'object'
                            ),
                        ]),
                    ]
                ),
            ),
            new OA\Response(
                response: '404',
                description: 'Not found',
                content: new OA\JsonContent(allOf: [
                    new OA\Schema(ref:  '#/components/schemas/BadResponse'),
                    new OA\Schema(properties: [
                        new OA\Property(property:'error_message', example:'Entity not found'),
                        new OA\Property(property: 'error_code', example: 404, nullable: true),
                    ]),
                ])
            ),
        ]
    )]
    public function view(#[RouteArgument('id')] int $id): Response
    {
        return $this->responseFactory->createResponse(
            [
                'post' => $this->postFormatter->format(
                    $this->blogService->getPost($id)
                ),
            ]
        );
    }

    #[OA\Post(
        path: '/blog/',
        description: '',
        summary: 'Creates a blog post',
        security: [new OA\SecurityScheme(ref: '#/components/securitySchemes/ApiKey')],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
            allOf: [
                new OA\Schema(ref: '#/components/schemas/EditPostRequest'),
            ]
        )),
        tags: ['blog'],
        responses: [
            new OA\Response(
                response: '200',
                description: 'Success',
                content: new OA\JsonContent(ref: '#/components/schemas/Response')
            ),
            new OA\Response(
                response: '400',
                description: 'Bad request',
                content: new OA\JsonContent(ref:  '#/components/schemas/BadResponse')
            ),
        ]
    )]
    public function create(EditPostRequest $postRequest, UserRequest $userRequest): Response
    {
        $post = $this->postBuilder->build(new Post(), $postRequest);
        $post->setUser($userRequest->getUser());

        $this->postRepository->save($post);

        return $this->responseFactory->createResponse();
    }

    #[OA\Put(
        path: '/blog/{id}',
        description: '',
        summary: 'Updates a blog post with a given ID',
        security: [new OA\SecurityScheme(ref: '#/components/securitySchemes/ApiKey')],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
            allOf: [
                new OA\Schema(ref: '#/components/schemas/EditPostRequest'),
            ]
        )),
        tags: ['blog'],
        parameters: [
            new OA\Parameter(parameter: 'id', name: 'id', in: 'path', schema: new OA\Schema(type: 'int', example: '2')),
        ],
        responses: [
            new OA\Response(
                response: '200',
                description: 'Success',
                content: new OA\JsonContent(ref: '#/components/schemas/Response')
            ),
            new OA\Response(
                response: '400',
                description: 'Bad request',
                content: new OA\JsonContent(ref:  '#/components/schemas/BadResponse')
            ),
        ]
    )]
    public function update(EditPostRequest $postRequest, #[RouteArgument('id')] int $id): Response
    {
        $post = $this->postBuilder->build(
            $this->blogService->getPost($id),
            $postRequest
        );

        $this->postRepository->save($post);

        return $this->responseFactory->createResponse();
    }
}
