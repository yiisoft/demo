<?php

declare(strict_types=1);

namespace App\User;

use Yiisoft\Auth\Middleware\Authentication;
use Yiisoft\RequestModel\RequestModel;

final class UserRequest extends RequestModel
{
    public function getUser(): User
    {
        /**
         * @var User $identity
         */
        return $this->getAttributeValue('attributes.' . Authentication::class);
    }
}
