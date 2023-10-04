<?php

declare(strict_types=1);

namespace App;

use OpenApi\Annotations as OA;
use Psr\Http\Message\ResponseInterface;
use Yiisoft\DataResponse\DataResponseFactoryInterface;

/**
 * @OA\Info(title="Yii API application", version="1.0")
 */
class InfoController
{
    public function __construct(private VersionProvider $versionProvider)
    {
    }

    /**
     * @OA\Get(
     *     path="/",
     *     summary="Returns info about the API",
     *     description="",
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
    public function index(DataResponseFactoryInterface $responseFactory): ResponseInterface
    {
        return $responseFactory->createResponse(['version' => $this->versionProvider->version, 'author' => 'yiisoft']);
    }
}
