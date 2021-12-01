<?php

declare(strict_types=1);

use App\Auth\Identity;
use App\Auth\IdentityRepository;
use Cycle\ORM\ORMInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Yiisoft\Access\AccessCheckerInterface;
use Yiisoft\Auth\IdentityRepositoryInterface;
use Yiisoft\Definitions\Reference;
use Yiisoft\Cookies\CookieEncryptor;
use Yiisoft\Cookies\CookieMiddleware;
use Yiisoft\Cookies\CookieSigner;
use Yiisoft\Session\SessionInterface;
use Yiisoft\User\CurrentUser;
use Yiisoft\User\Login\Cookie\CookieLogin;

/** @var array $params */

return [
    IdentityRepositoryInterface::class => static function (ContainerInterface $container): IdentityRepository {
        return $container->get(ORMInterface::class)->getRepository(Identity::class);
    },

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
