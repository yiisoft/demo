<?php

declare(strict_types=1);

namespace App\Infrastructure\IO\Http\Blog\PutUpdate;

use App\Application\Blog\UseCase\Post\Update\Command;
use App\Application\Blog\UseCase\Post\Update\Handler;
use App\Infrastructure\IO\Http\Blog\PutUpdate\Request\Request;
use App\Infrastructure\IO\Http\Blog\PutUpdate\Response\ResponseFactory;
use OpenApi\Annotations as OA;
use Psr\Http\Message\ResponseInterface;

/**
 * @OA\Tag(
 *     name="blog",
 *     description="Blog"
 * )
 *
 * @OA\Put(
 *     tags={"blog"},
 *     path="/{locale}/blog/{id}",
 *     summary="Updates a blog post with a given ID",
 *     description="",
 *     security={{"ApiKey": {}}},
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
 *              ref="#/components/schemas/Response"
 *          )
 *    ),
 *     @OA\RequestBody(
 *          required=true,
 *          @OA\MediaType(
 *              mediaType="application/json",
 *              @OA\Schema(ref="#/components/schemas/BlogUpdateRequest"),
 *          ),
 *     )
 * )
 */
final class Action
{
    public function __invoke(
        Request $request,
        Handler $handler,
        ResponseFactory $responseFactory,
    ): ResponseInterface {
        $command = new Command(
            $request->getId(),
            $request->getTitle(),
            $request->getText(),
            $request->getStatus(),
        );
        $handler->handle($command);

        return $responseFactory->create();
    }
}
