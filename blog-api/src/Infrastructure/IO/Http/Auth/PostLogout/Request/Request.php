<?php

declare(strict_types=1);

namespace App\Infrastructure\IO\Http\Auth\PostLogout\Request;

use App\Application\User\Entity\User;
use Yiisoft\Auth\Middleware\Authentication;
use Yiisoft\Input\Http\Attribute\Parameter\Request as RequestParameter;
use Yiisoft\Input\Http\RequestInputInterface;

final class Request implements RequestInputInterface
{
    #[RequestParameter(Authentication::class)]
    private User $user;

    public function getUser(): User
    {
        return $this->user;
    }
}
