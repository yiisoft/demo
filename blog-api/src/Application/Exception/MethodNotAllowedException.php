<?php

declare(strict_types=1);

namespace App\Application\Exception;

use LogicException;
use Throwable;

final class MethodNotAllowedException extends LogicException implements ApplicationException
{
    public function __construct(
        $message = 'Method is not implemented yet',
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
