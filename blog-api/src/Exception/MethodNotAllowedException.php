<?php

declare(strict_types=1);

namespace App\Exception;

use LogicException;
use Throwable;
use Yiisoft\Http\Status;

final class MethodNotAllowedException extends LogicException implements ApplicationException
{
    public function __construct(
        $message = 'Method is not implemented yet',
        $code = Status::METHOD_NOT_ALLOWED,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
