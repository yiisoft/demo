<?php

declare(strict_types=1);

use App\Auth\Identity;
use Cycle\ORM\RepositoryInterface;
use Cycle\ORM\ORMInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Yiisoft\Access\AccessCheckerInterface;
use Yiisoft\Auth\IdentityRepositoryInterface;
use Yiisoft\Cookies\CookieEncryptor;
use Yiisoft\Cookies\CookieMiddleware;
use Yiisoft\Cookies\CookieSigner;
use Yiisoft\Definitions\Reference;
use Yiisoft\Session\SessionInterface;
use Yiisoft\User\CurrentUser;
use Yiisoft\User\Login\Cookie\CookieLogin;

/** 
 * @var array $params
 * @var array $params['yiisoft/cookies']
 * @var string $params['yiisoft/cookies']['secretKey']
 */
$secretKey = $params['yiisoft/cookies']['secretKey'];
return [
    IdentityRepositoryInterface::class => static function (ContainerInterface $container): RepositoryInterface {
        return $container
            ->get(ORMInterface::class)
            ->getRepository(Identity::class);
    },
    CookieMiddleware::class => static fn (CookieLogin $cookieLogin, LoggerInterface $logger) => new CookieMiddleware(
        $logger,
        new CookieEncryptor($secretKey),
        new CookieSigner($secretKey),
        [$cookieLogin->getCookieName() => CookieMiddleware::SIGN],
    ),
    CurrentUser::class => [
        'withSession()' => [Reference::to(SessionInterface::class)],
        'withAccessChecker()' => [Reference::to(AccessCheckerInterface::class)],
        'reset' => function (CurrentUser $currentUser) {
            $currentUser->clear();
        },
    ],
];
