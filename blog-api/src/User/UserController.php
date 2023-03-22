<?php

declare(strict_types=1);

namespace App\User;

use App\Exception\NotFoundException;
use App\RestControllerTrait;
use OpenApi\Annotations as OA;
use Psr\Http\Message\ResponseInterface;
use Yiisoft\DataResponse\DataResponseFactoryInterface;
use Yiisoft\RequestModel\Attribute\Route;

/**
 * @OA\Tag(
 *     name="user",
 *     description="Users"
 * )
 */
final class UserController
{
    use RestControllerTrait;

    private DataResponseFactoryInterface $responseFactory;
    private UserRepository $userRepository;
    private UserFormatter $userFormatter;

    public function __construct(
        DataResponseFactoryInterface $responseFactory,
        UserRepository $userRepository,
        UserFormatter $userFormatter
    ) {
        $this->responseFactory = $responseFactory;
        $this->userRepository = $userRepository;
        $this->userFormatter = $userFormatter;
    }

    /**
     * @OA\Get(
     *     tags={"user"},
     *     path="/users",
     *     summary="Returns paginated users",
     *     description="",
     *     security={{"ApiKey": {}}},
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
     *                              property="users",
     *                              type="array",
     *
     *                              @OA\Items(ref="#/components/schemas/User")
     *                          ),
     *                      ),
     *                  ),
     *              },
     *          )
     *    ),
     * )
     */
    public function list(): ResponseInterface
    {
        $dataReader = $this->userRepository->findAllOrderByLogin();
        $result = [];
        foreach ($dataReader->read() as $user) {
            $result[] = $this->userFormatter->format($user);
        }

        return $this->responseFactory->createResponse(
            [
                'users' => $result,
            ]
        );
    }

    /**
     * @OA\Get(
     *     tags={"user"},
     *     path="/users/{id}",
     *     summary="Returns a user with a given ID",
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
     *              allOf={
     *
     *                  @OA\Schema(ref="#/components/schemas/Response"),
     *                  @OA\Schema(
     *
     *                      @OA\Property(
     *                          property="data",
     *                          type="object",
     *                          @OA\Property(
     *                              property="user",
     *                              type="object",
     *                              ref="#/components/schemas/User"
     *                          ),
     *                      ),
     *                  ),
     *              },
     *          )
     *    ),
     * )
     */
    public function get(#[Route('id')] int $id): ResponseInterface
    {
        /**
         * @var User $user
         */
        $user = $this->userRepository->findByPK($id);
        if ($user === null) {
            throw new NotFoundException();
        }

        return $this->responseFactory->createResponse(
            [
                'user' => $this->userFormatter->format($user),
            ]
        );
    }
}
