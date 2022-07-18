<?php

declare(strict_types=1);

namespace App\Auth\Form;

class LoginExistException extends \InvalidArgumentException
{
    /**
     * @var string
     */
    protected $message = 'User with this login already exists.';
}
