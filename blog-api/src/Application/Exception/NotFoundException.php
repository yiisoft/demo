<?php

declare(strict_types=1);

namespace App\Application\Exception;

use Exception;
use Throwable;

final class NotFoundException extends Exception implements ApplicationException
{
    public function __construct($message = 'Entity not found', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
