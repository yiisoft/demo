<?php

declare(strict_types=1);

namespace App\Blog;

use App\Formatter\PaginatorFormatter;
use App\User\UserRequest;
use OpenApi\Annotations as OA;
use Psr\Http\Message\ResponseInterface as Response;
use Yiisoft\DataResponse\DataResponseFactoryInterface;
use Yiisoft\RequestModel\Attribute\Query;
use Yiisoft\RequestModel\Attribute\Route;

/**
 * @OA\Tag(
 *     name="blog",
 *     description="Blog"
 * )
 *
 * @OA\Parameter(
 *
 *      @OA\Schema(
 *          type="int",
 *          example="2"
 *      ),
 *      in="query",
 *      name="page",
 *      parameter="PageRequest"
 * )
 */
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

    /**
     * @OA\Get(
     *     tags={"blog"},
     *     path="/blog/",
     *     summary="Returns paginated blog posts",
     *     description="",
     *
     *     @OA\Parameter(ref="#/components/parameters/PageRequest"),
     *
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *
     *          @OA\JsonContent(
     *              allOf={
     *
     *                  @OA\Schema(ref="#/components/schemas/Response"),
     *                  @OA\Schema(
     *
     *                      @OA\Property(
     *                          property="data",
     *                          type="object",
     *                          @OA\Property(
     *                              property="posts",
     *                              type="array",
     *
     *                              @OA\Items(ref="#/components/schemas/Post")
     *                          ),
     *
     *                          @OA\Property(
     *                              property="paginator",
     *                              type="object",
     *                              ref="#/components/schemas/Paginator"
     *                          ),
     *                      ),
     *                  ),
     *              },
     *          )
     *    ),
     * )
     */
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

    /**
     * @OA\Get(
     *     tags={"blog"},
     *     path="/blog/{id}",
     *     summary="Returns a post with a given ID",
     *     description="",
     *
     *     @OA\Parameter(
     *
     *          @OA\Schema(type="int", example="2"),
     *          in="path",
     *          name="id",
     *          parameter="id"
     *     ),
     *
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *
     *          @OA\JsonContent(
     *              allOf={
     *
     *                  @OA\Schema(ref="#/components/schemas/Response"),
     *                  @OA\Schema(
     *
     *                      @OA\Property(
     *                          property="data",
     *                          type="object",
     *                          @OA\Property(
     *                              property="post",
     *                              type="object",
     *                              ref="#/components/schemas/Post"
     *                          ),
     *                      ),
     *                  ),
     *              },
     *          )
     *    ),
     *
     *    @OA\Response(
     *          response="404",
     *          description="Not found",
     *
     *          @OA\JsonContent(
     *              allOf={
     *
     *                  @OA\Schema(ref="#/components/schemas/BadResponse"),
     *                  @OA\Schema(
     *
     *                      @OA\Property(property="error_message", example="Entity not found"),
     *                      @OA\Property(property="error_code", nullable=true, example=404)
     *                  ),
     *              },
     *          )
     *    ),
     * )
     */
    public function view(#[Route('id')] int $id): Response
    {
        return $this->responseFactory->createResponse(
            [
                'post' => $this->postFormatter->format(
                    $this->blogService->getPost($id)
                ),
            ]
        );
    }

    /**
     * @OA\Post(
     *     tags={"blog"},
     *     path="/blog",
     *     summary="Creates a blog post",
     *     description="",
     *     security={{"ApiKey": {}}},
     *
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *
     *          @OA\JsonContent(
     *              ref="#/components/schemas/Response"
     *          )
     *    ),
     *
     *     @OA\RequestBody(
     *          required=true,
     *
     *          @OA\MediaType(
     *              mediaType="application/json",
     *
     *              @OA\Schema(ref="#/components/schemas/EditPostRequest"),
     *          ),
     *     ),
     * )
     */
    public function create(EditPostRequest $postRequest, UserRequest $userRequest): Response
    {
        $post = $this->postBuilder->build(new Post(), $postRequest);
        $post->setUser($userRequest->getUser());

        $this->postRepository->save($post);

        return $this->responseFactory->createResponse();
    }

    /**
     * @OA\Put(
     *     tags={"blog"},
     *     path="/blog/{id}",
     *     summary="Updates a blog post with a given ID",
     *     description="",
     *     security={{"ApiKey": {}}},
     *
     *     @OA\Parameter(
     *
     *          @OA\Schema(type="int", example="2"),
     *          in="path",
     *          name="id",
     *          parameter="id"
     *     ),
     *
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *
     *          @OA\JsonContent(
     *              ref="#/components/schemas/Response"
     *          )
     *    ),
     *
     *     @OA\RequestBody(
     *          required=true,
     *
     *          @OA\MediaType(
     *              mediaType="application/json",
     *
     *              @OA\Schema(ref="#/components/schemas/EditPostRequest"),
     *          ),
     *     )
     * )
     */
    public function update(EditPostRequest $postRequest, #[Route('id')] int $id): Response
    {
        $post = $this->postBuilder->build(
            $this->blogService->getPost($id),
            $postRequest
        );

        $this->postRepository->save($post);

        return $this->responseFactory->createResponse();
    }
}
