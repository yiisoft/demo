<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use App\Application\Exception\BadRequestException;
use App\Application\Exception\NotFoundException;
use Throwable;
use Yiisoft\Http\Status;

final class HttpExceptionMapper
{
    /**
     * @var array<string, int>
     */
    private const EXCEPTION_CODE_MAP = [
        NotFoundException::class => Status::NOT_FOUND,
        BadRequestException::class => Status::BAD_REQUEST,
    ];

    /**
     * @psalm-suppress InvalidArrayOffset
     */
    public function getCode(Throwable $e): int
    {
        return self::EXCEPTION_CODE_MAP[get_class($e)] ?? $e->getCode();
    }
}
