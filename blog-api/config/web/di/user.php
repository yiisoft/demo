<?php

declare(strict_types=1);

use App\Application\User\Entity\UserRepository;
use App\Infrastructure\Http\Handler\AuthRequestErrorHandler;
use Yiisoft\Auth\AuthenticationMethodInterface;
use Yiisoft\Auth\IdentityRepositoryInterface;
use Yiisoft\Auth\IdentityWithTokenRepositoryInterface;
use Yiisoft\Auth\Method\HttpHeader;
use Yiisoft\Auth\Middleware\Authentication;
use Yiisoft\Definitions\Reference;

/** @var array $params */

return [
    IdentityRepositoryInterface::class => UserRepository::class,
    IdentityWithTokenRepositoryInterface::class => UserRepository::class,
    AuthenticationMethodInterface::class => HttpHeader::class,
    Authentication::class => [
        'class' => Authentication::class,
        '__construct()' => [
            'authenticationFailureHandler' => Reference::to(AuthRequestErrorHandler::class),
        ],
    ],
];
