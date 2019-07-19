<?php

use App\Factory\LoggerFactory;
use App\Factory\MailerFactory;
use Psr\Log\LoggerInterface;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Cache\ArrayCache;
use Yiisoft\Cache\Cache;
use Yiisoft\Cache\CacheInterface;
use Yiisoft\Log\FileRotator;
use Yiisoft\Log\FileRotatorInterface;
use Yiisoft\Log\Logger;
use Yiisoft\Mailer\MailerInterface;
use Yiisoft\Mailer\SwiftMailer\Mailer;

$params = $params ?? [];

return [
    Aliases::class => [
        '@root' => dirname(__DIR__),
        '@views' => '@root/views',
        '@resources' => '@root/resources',
    ],
    CacheInterface::class => [
        '__class' => Cache::class,
        'handler' => [
            '__class' => ArrayCache::class,
        ],
    ],
    FileRotatorInterface::class => [
        '__class' => FileRotator::class,
        '__construct()' => [
            10
        ]
    ],
    LoggerInterface::class => Logger::class,
    Logger::class => new LoggerFactory(),
    \Swift_Transport::class => \Swift_SmtpTransport::class,    
    \Swift_SmtpTransport::class => [
        '__class' => \Swift_SmtpTransport::class,
        '__construct()' => [
            'host' => $params['mailer.host'],
            'port' => $params['mailer.port'],
            'encryption' => $params['mailer.encryption'],
        ],
        'setUsername()' => [$params['mailer.username']],
        'setPassword()' => [$params['mailer.password']],
    ],
    MailerInterface::class => Mailer::class,
    Mailer::class => new MailerFactory(),
];
