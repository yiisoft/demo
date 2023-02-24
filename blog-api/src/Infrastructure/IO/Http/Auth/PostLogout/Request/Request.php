<?php

declare(strict_types=1);

namespace App\Infrastructure\IO\Http\Auth\PostLogout\Request;

use App\Application\User\Entity\User;
use Yiisoft\Auth\Middleware\Authentication;
use Yiisoft\RequestModel\RequestModel;

final class Request extends RequestModel
{
    public function getUser(): User
    {
        /**
         * @var User $identity
         */
        return $this->getAttributeValue('attributes.' . Authentication::class);
    }
}
