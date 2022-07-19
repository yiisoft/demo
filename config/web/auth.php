<?php

declare(strict_types=1);

use Psr\Log\LoggerInterface;
use Yiisoft\Access\AccessCheckerInterface;
use Yiisoft\Definitions\Reference;
use Yiisoft\Cookies\CookieEncryptor;
use Yiisoft\Cookies\CookieMiddleware;
use Yiisoft\Cookies\CookieSigner;
use Yiisoft\Session\SessionInterface;
use Yiisoft\User\CurrentUser;
use Yiisoft\User\Login\Cookie\CookieLogin;

/** @var array $params */

return [
    CookieMiddleware::class => static fn (CookieLogin $cookieLogin, LoggerInterface $logger) => new CookieMiddleware(
        $logger,
        new CookieEncryptor($params['yiisoft/cookies']['secretKey']),
        new CookieSigner($params['yiisoft/cookies']['secretKey']),
        [$cookieLogin->getCookieName() => CookieMiddleware::SIGN],
    ),

    CurrentUser::class => [
        'withSession()' => [Reference::to(SessionInterface::class)],
        'withAccessChecker()' => [Reference::to(AccessCheckerInterface::class)],
        'reset' => function () {
            $this->clear();
        },
    ],
];
