<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;
use Throwable;
use Yiisoft\Http\Status;

final class NotFoundException extends Exception implements ApplicationException
{
    public function __construct($message = 'Entity not found', $code = Status::NOT_FOUND, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
