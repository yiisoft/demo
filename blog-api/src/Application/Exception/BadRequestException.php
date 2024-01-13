<?php

declare(strict_types=1);

namespace App\Application\Exception;

use Exception;
use Throwable;

final class BadRequestException extends Exception implements ApplicationException
{
    public function __construct($message = 'Bad request', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
