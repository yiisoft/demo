<?php

declare(strict_types=1);

namespace App\Infrastructure\IO\Http\Auth\PostLogout;

use App\Application\User\Service\UserService;
use App\Infrastructure\IO\Http\Auth\PostLogout\Request\Request;
use OpenApi\Annotations as OA;
use Psr\Http\Message\ResponseInterface;
use Yiisoft\DataResponse\DataResponseFactoryInterface;

/**
 * @OA\Tag(
 *     name="auth",
 *     description="Authentication"
 * )
 *
 * @OA\Post(
 *     tags={"auth"},
 *     path="/{locale}/auth/logout",
 *     summary="Logout",
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
 *          @OA\JsonContent(ref="#/components/schemas/Response")
 *    ),
 *    @OA\Response(
 *          response="400",
 *          description="Bad request",
 *          @OA\JsonContent(ref="#/components/schemas/BadResponse")
 *     ),
 * )
 */
final class Action
{
    public function __construct(
        private DataResponseFactoryInterface $responseFactory,
        private UserService $userService,
    ) {
    }

    public function __invoke(Request $request): ResponseInterface
    {
        $this->userService->logout($request->getUser());
        return $this->responseFactory->createResponse();
    }
}
