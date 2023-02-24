<?php

declare(strict_types=1);

namespace App\Infrastructure\IO\Http\Blog\GetIndex;

use App\Application\Blog\UseCase\Post\GetPageList\Command;
use App\Application\Blog\UseCase\Post\GetPageList\Handler;
use App\Infrastructure\IO\Http\Blog\GetIndex\Request\Request;
use App\Infrastructure\IO\Http\Blog\GetIndex\Response\ResponseFactory;
use OpenApi\Annotations as OA;
use Psr\Http\Message\ResponseInterface;

/**
 * @OA\Tag(
 *     name="blog",
 *     description="Blog"
 * )
 *
 * @OA\Get(
 *     tags={"blog"},
 *     path="/{locale}/blog/",
 *     summary="Returns paginated blog posts",
 *     description="",
 *     @OA\Parameter(
 *          @OA\Schema(type="string", example="en"),
 *          in="path",
 *          name="locale",
 *          parameter="locale"
 *     ),
 *     @OA\Parameter(ref="#/components/parameters/BlogIndexRequest"),
 *     @OA\Response(
 *          response="200",
 *          description="Success",
 *          @OA\JsonContent(
 *              allOf={
 *                  @OA\Schema(ref="#/components/schemas/Response"),
 *                  @OA\Schema(
 *                      @OA\Property(
 *                          property="data",
 *                          type="object",
 *                          @OA\Property(
 *                              property="posts",
 *                              type="array",
 *                              @OA\Items(ref="#/components/schemas/BlogIndexPost")
 *                          ),
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
final class Action
{
    public function __invoke(
        Request $request,
        Handler $handler,
        ResponseFactory $responseFactory,
    ): ResponseInterface {
        $command = new Command($request->getPage());
        $paginator = $handler->handle($command);

        return $responseFactory->create($paginator);
    }
}
