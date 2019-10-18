<?php

use App\Factory\CycleDbalFactory;
use App\Factory\CycleOrmFactory;
use App\Factory\LoggerFactory;
use App\Factory\MailerFactory;
use App\Helper\EntityFinderHelper;
use Cycle\ORM\ORMInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Spiral\Database\DatabaseManager;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Cache\ArrayCache;
use Yiisoft\Cache\Cache;
use Yiisoft\Cache\CacheInterface;
use Yiisoft\Log\Target\File\FileRotator;
use Yiisoft\Log\Target\File\FileRotatorInterface;
use Yiisoft\Mailer\MailerInterface;

$params = $params ?? [];

return [
    Aliases::class => [
        '@root' => dirname(__DIR__),
        '@views' => '@root/views',
        '@resources' => '@root/resources',
        '@src' => '@root/src',
        '@runtime' => '@root/runtime',
    ],
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
    Swift_Transport::class => Swift_SmtpTransport::class,
    Swift_SmtpTransport::class => [
        '__class' => Swift_SmtpTransport::class,
        '__construct()' => [
            'host' => $params['mailer.host'],
            'port' => $params['mailer.port'],
            'encryption' => $params['mailer.encryption'],
        ],
        'setUsername()' => [$params['mailer.username']],
        'setPassword()' => [$params['mailer.password']],
    ],
    MailerInterface::class => new MailerFactory(),


    // Cycle DBAL
    DatabaseManager::class => new CycleDbalFactory(),
    // Cycle ORM
    ORMInterface::class => new CycleOrmFactory(),
    // Cycle Entity Finder
    EntityFinderHelper::class => [
        '__class' => EntityFinderHelper::class,
        'addPaths()' => [
            'paths' => $params['entityPaths'],
        ],
    ],

];
