<?php

declare(strict_types=1);

namespace App\User;

use Yiisoft\Auth\Middleware\Authentication;
use Yiisoft\Input\Http\Attribute\Parameter\Request;
use Yiisoft\Input\Http\RequestInputInterface;

final class UserRequest implements RequestInputInterface
{
    #[Request(Authentication::class)]
    private User $user;

    public function getUser(): User
    {
        return $this->user;
    }
}
