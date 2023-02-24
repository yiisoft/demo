<?php

declare(strict_types=1);

namespace App\Infrastructure\IO\Http\User\GetIndex;

use App\Application\User\Entity\UserRepository;
use App\Infrastructure\IO\Http\User\GetIndex\Response\ResponseFactory;
use OpenApi\Annotations as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * @OA\Tag(
 *     name="user",
 *     description="Users"
 * )
 *
 * @OA\Get(
 *     tags={"user"},
 *     path="/{locale}/users/",
 *     summary="Returns paginated users",
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
 *              allOf={
 *                  @OA\Schema(ref="#/components/schemas/Response"),
 *                  @OA\Schema(
 *                      @OA\Property(
 *                          property="data",
 *                          type="object",
 *                          @OA\Property(
 *                              property="users",
 *                              type="array",
 *                              @OA\Items(ref="#/components/schemas/UserIndexResponse")
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
        ResponseFactory $responseFactory,
        UserRepository $userRepository,
        LoggerInterface $logger
    ): ResponseInterface {
        $dataReader = $userRepository->findAllOrderByLogin();

        $logger->debug('Collected {count} users', ['count' => $dataReader->count()]);

        return $responseFactory->create($dataReader);
    }
}
