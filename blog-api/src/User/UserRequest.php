<?php

declare(strict_types=1);

namespace App\User;

use Vjik\InputHttp\Attribute\Parameter\Request;
use Vjik\InputHttp\RequestModelInterface;
use Yiisoft\Auth\Middleware\Authentication;

final class UserRequest implements RequestModelInterface
{
    #[Request(Authentication::class)]
    private User $user;

    public function getUser(): User
    {
        return $this->user;
    }
}
