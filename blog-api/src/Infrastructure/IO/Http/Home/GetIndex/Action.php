<?php

declare(strict_types=1);

namespace App\Infrastructure\IO\Http\Home\GetIndex;

use App\Infrastructure\VersionProvider;
use OpenApi\Annotations as OA;
use Psr\Http\Message\ResponseInterface;
use Yiisoft\DataResponse\DataResponseFactoryInterface;

/**
 * @OA\Info(title="Yii API application", version="1.0")
 *
 * @OA\Get(
 *     path="/{locale}/",
 *     summary="Returns info about the API",
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
 *                          @OA\Property(
 *                              property="version",
 *                              type="string",
 *                              example="3.0"
 *                          ),
 *                          @OA\Property(
 *                              property="author",
 *                              type="string",
 *                              example="yiisoft"
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
    public function __construct(
        private readonly VersionProvider $versionProvider,
    ) {
    }

    public function __invoke(DataResponseFactoryInterface $responseFactory): ResponseInterface
    {
        return $responseFactory->createResponse(['version' => $this->versionProvider->version, 'author' => 'yiisoft']);
    }
}
