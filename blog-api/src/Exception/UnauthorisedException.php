<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;
use Throwable;
use Yiisoft\Http\Status;

final class UnauthorisedException extends Exception implements ApplicationException
{
    public function __construct($message = 'Unauthorised request', $code = Status::UNAUTHORIZED, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
