<?php

use App\Factory\LoggerFactory;
use App\Factory\MailerFactory;
use App\Timer;
use Psr\Log\LoggerInterface;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Cache\File\FileCache;
use Psr\Container\ContainerInterface;
use Psr\SimpleCache\CacheInterface;
use Yiisoft\Cache\Cache;
use Yiisoft\Cache\CacheInterface as YiiCacheInterface;
use Yiisoft\Log\Target\File\FileRotator;
use Yiisoft\Log\Target\File\FileRotatorInterface;
use Yiisoft\Mailer\MailerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Yiisoft\EventDispatcher\Dispatcher\Dispatcher;
use Yiisoft\EventDispatcher\Provider\Provider;

/**
 * @var array $params
 */

$timer = new Timer();
$timer->start('overall');

return [
    ContainerInterface::class => static function (ContainerInterface $container) {
        return $container;
    },
    // event dispatcher
    ListenerProviderInterface::class => Provider::class,
    EventDispatcherInterface::class => Dispatcher::class,
    //cache
    CacheInterface::class => static function (ContainerInterface $container) {
        return new FileCache($container->get(Aliases::class)->get('@runtime/cache'));
    },
    YiiCacheInterface::class => Cache::class,
    LoggerInterface::class => new LoggerFactory(),

    FileRotatorInterface::class => [
        '__class' => FileRotator::class,
        '__construct()' => [
            10,
        ],
    ],
    //mail
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
    MailerInterface::class => new MailerFactory($params['mailer']['writeToFiles']),
    Timer::class => $timer,
];
