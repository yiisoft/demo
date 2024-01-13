<?php

declare(strict_types=1);

namespace App\Infrastructure\IO\Http\Auth\PostLogin;

use App\Application\User\Service\UserService;
use App\Infrastructure\IO\Http\Auth\PostLogin\Request\Request;
use OpenApi\Annotations as OA;
use Psr\Http\Message\ResponseInterface;
use Yiisoft\DataResponse\DataResponseFactoryInterface;

/**
 * @OA\Tag(
 *     name="auth",
 *     description="Authentication"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="ApiKey",
 *     type="apiKey",
 *     in="header",
 *     name="X-Api-Key"
 * )
 *
 * @OA\Post(
 *     tags={"auth"},
 *     path="/{locale}/auth/login",
 *     summary="Authenticate by params",
 *     description="",
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
 *              allOf={
 *                  @OA\Schema(ref="#/components/schemas/Response"),
 *                  @OA\Schema(
 *                      @OA\Property(
 *                          property="data",
 *                          type="object",
 *                          @OA\Property(property="token", format="string",
 *     example="uap4X5Bd7078lxIFvxAflcGAa5D95iSSZkNjg3XFrE2EBRBlbj"),
 *                      ),
 *                  ),
 *              },
 *          )
 *    ),
 *    @OA\Response(
 *          response="400",
 *          description="Bad request",
 *          @OA\JsonContent(ref="#/components/schemas/BadResponse")
 *     ),
 *     @OA\RequestBody(
 *          required=true,
 *          @OA\MediaType(
 *              mediaType="application/json",
 *              @OA\Schema(ref="#/components/schemas/LoginRequest"),
 *          ),
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
        $identity = $this->userService->login(
            $request->getLogin(),
            $request->getPassword()
        );

        return $this->responseFactory->createResponse([
            'token' => $identity->getToken(),
        ]);
    }
}
