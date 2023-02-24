<?php

declare(strict_types=1);

namespace App\Infrastructure\IO\Http\Blog\GetView;

use App\Application\Blog\UseCase\Post\GetById\Command;
use App\Application\Blog\UseCase\Post\GetById\Handler;
use App\Infrastructure\IO\Http\Blog\GetView\Request\Request;
use App\Infrastructure\IO\Http\Blog\GetView\Response\ResponseFactory;
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
 *     path="/{locale}/blog/{id}",
 *     summary="Returns a post with a given ID",
 *     description="",
 *     @OA\Parameter(
 *          @OA\Schema(type="string", example="en"),
 *          in="path",
 *          name="locale",
 *          parameter="locale"
 *     ),
 *     @OA\Parameter(
 *          @OA\Schema(type="int", example="2"),
 *          in="path",
 *          name="id",
 *          parameter="id"
 *     ),
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
 *                              property="post",
 *                              type="object",
 *                              ref="#/components/schemas/BlogViewPost"
 *                          ),
 *                      ),
 *                  ),
 *              },
 *          )
 *    ),
 *    @OA\Response(
 *          response="404",
 *          description="Not found",
 *          @OA\JsonContent(
 *              allOf={
 *                  @OA\Schema(ref="#/components/schemas/BadResponse"),
 *                  @OA\Schema(
 *                      @OA\Property(property="error_message", example="Entity not found"),
 *                      @OA\Property(property="error_code", nullable=true, example=404)
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
        $command = new Command($request->getId());
        $post = $handler->handle($command);

        return $responseFactory->create($post);
    }
}
