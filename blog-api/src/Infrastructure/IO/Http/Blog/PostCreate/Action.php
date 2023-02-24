<?php

declare(strict_types=1);

namespace App\Infrastructure\IO\Http\Blog\PostCreate;

use App\Application\Blog\UseCase\Post\Create\Command;
use App\Application\Blog\UseCase\Post\Create\Handler;
use App\Infrastructure\IO\Http\Blog\PostCreate\Request\Request;
use App\Infrastructure\IO\Http\Blog\PostCreate\Response\ResponseFactory;
use OpenApi\Annotations as OA;
use Psr\Http\Message\ResponseInterface;

/**
 * @OA\Tag(
 *     name="blog",
 *     description="Blog"
 * )
 *
 * @OA\Post(
 *     tags={"blog"},
 *     path="/{locale}/blog/",
 *     summary="Creates a blog post",
 *     description="",
 *     security={{"ApiKey": {}}},
 *     @OA\Parameter(
 *          @OA\Schema(type="string", example="en"),
 *          in="path",
 *          name="locale",
 *          parameter="locale"
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
 *              @OA\Schema(ref="#/components/schemas/BlogPostCreate"),
 *          ),
 *     ),
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
            $request->getTitle(),
            $request->getText(),
            $request->getStatus(),
            $request->getUser(),
        );
        $handler->handle($command);

        return $responseFactory->create();
    }
}
