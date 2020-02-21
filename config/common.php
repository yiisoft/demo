<?php

use App\Factory\LoggerFactory;
use App\Factory\MailerFactory;
use App\Parameters;
use App\Timer;
use Psr\Log\LoggerInterface;
use Yiisoft\Log\Target\File\FileRotator;
use Yiisoft\Log\Target\File\FileRotatorInterface;
use Yiisoft\Mailer\MailerInterface;

/**
 * @var array $params
 */

$timer = new Timer();
$timer->start('overall');

return [
    \Psr\SimpleCache\CacheInterface::class => \Yiisoft\Cache\ArrayCache::class,
    \Yiisoft\Cache\CacheInterface::class => \Yiisoft\Cache\Cache::class,
    Parameters::class => static function () use ($params) {
        return new Parameters($params);
    },
    LoggerInterface::class => new LoggerFactory(),
    FileRotatorInterface::class => [
        '__class' => FileRotator::class,
        '__construct()' => [
            10,
        ],
    ],
    Swift_Transport::class => Swift_SmtpTransport::class,
    Swift_SmtpTransport::class => [
        '__class' => Swift_SmtpTransport::class,
        '__construct()' => [
            'host' => $params['mailer']['host'],
            'port' => $params['mailer']['port'],
            'encryption' => $params['mailer']['encryption'],
        ],
        'setUsername()' => [$params['mailer']['username']],
        'setPassword()' => [$params['mailer']['password']],
    ],
    MailerInterface::class => new MailerFactory(),
    Timer::class => $timer,
];
