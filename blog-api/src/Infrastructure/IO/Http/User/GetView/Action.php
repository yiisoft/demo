<?php

declare(strict_types=1);

namespace App\Infrastructure\IO\Http\User\GetView;

use App\Application\Exception\NotFoundException;
use App\Application\User\Entity\User;
use App\Application\User\Entity\UserRepository;
use App\Infrastructure\IO\Http\User\GetView\Response\ResponseFactory;
use OpenApi\Annotations as OA;
use Psr\Http\Message\ResponseInterface;
use Yiisoft\Router\CurrentRoute;

/**
 * @OA\Tag(
 *     name="user",
 *     description="Users"
 * )
 *
 * @OA\Get(
 *     tags={"user"},
 *     path="/{locale}/users/{id}",
 *     summary="Returns a user with a given ID",
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
 *              allOf={
 *                  @OA\Schema(ref="#/components/schemas/Response"),
 *                  @OA\Schema(
 *                      @OA\Property(
 *                          property="data",
 *                          type="object",
 *                          @OA\Property(
 *                              property="user",
 *                              type="object",
 *                              ref="#/components/schemas/UserViewResponse"
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
        CurrentRoute $currentRoute,
        UserRepository $userRepository,
        ResponseFactory $responseFactory,
    ): ResponseInterface {
        /**
         * @var User $user
         */
        $user = $userRepository->findByPK($currentRoute->getArgument('id'));
        if ($user === null) {
            throw new NotFoundException();
        }

        return $responseFactory->create($user);
    }
}
