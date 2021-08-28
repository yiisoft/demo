<?php

declare(strict_types=1);

use Yiisoft\Auth\AuthenticationMethodInterface;
use Yiisoft\User\CurrentUser;
use Yiisoft\User\Login\Cookie\CookieLoginMiddleware;
use Yiisoft\User\Login\Cookie\CookieLogin;
use Yiisoft\User\UserAuth;

/** @var array $params */

return [
    CurrentUser::class => [
        'reset' => function () {
            $this->identity = null;
            $this->identityOverride = null;
        },
    ],
    UserAuth::class => [
        'class' => UserAuth::class,
        'withAuthUrl()' => [$params['yiisoft/user']['authUrl']],
    ],

    AuthenticationMethodInterface::class => UserAuth::class,

    CookieLoginMiddleware::class => [
        '__construct()' => [
            'addCookie' => $params['yiisoft/user']['cookieLogin']['addCookie'],
        ],
    ],

    CookieLogin::class => [
        '__construct()' => [
            'duration' => new \DateInterval($params['yiisoft/user']['cookieLogin']['duration']),
        ],
    ],
];
