<?php

use App\Factory\LoggerFactory;
use App\Factory\MailerFactory;
use App\Parameters;
use Psr\Log\LoggerInterface;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Cache\ArrayCache;
use Yiisoft\Cache\Cache;
use Yiisoft\Cache\CacheInterface;
use Yiisoft\Log\Target\File\FileRotator;
use Yiisoft\Log\Target\File\FileRotatorInterface;
use Yiisoft\Mailer\MailerInterface;

/**
 * @var array $params
 */

return [
    Aliases::class => $params['aliases'],
    Psr\SimpleCache\CacheInterface::class => ArrayCache::class,
    CacheInterface::class => Cache::class,
    Parameters::class => static function () use ($params) {
        return new Parameters($params);
    },
    CacheInterface::class => [
        '__class' => Cache::class,
        'handler' => [
            '__class' => ArrayCache::class,
        ],
    ],
    LoggerInterface::class => new LoggerFactory(),
    FileRotatorInterface::class => [
        '__class' => FileRotator::class,
        '__construct()' => [
            10
        ]
    ],
    \Swift_Transport::class => \Swift_SmtpTransport::class,
    \Swift_SmtpTransport::class => [
        '__class' => \Swift_SmtpTransport::class,
        '__construct()' => [
            'host' => $params['mailer']['host'],
            'port' => $params['mailer']['port'],
            'encryption' => $params['mailer']['encryption'],
        ],
        'setUsername()' => [$params['mailer']['username']],
        'setPassword()' => [$params['mailer']['password']],
    ],
    MailerInterface::class => new MailerFactory(),
];
