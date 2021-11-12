<?php

declare(strict_types=1);

use Psr\Log\LoggerInterface;
use Yiisoft\Definitions\ReferencesArray;
use Yiisoft\Log\Logger;
use Yiisoft\Log\Target\File\FileTarget;

/** @var array $params */

return [
    LoggerInterface::class => [
        'class' => Logger::class,
        '__construct()' => [
            'targets' => ReferencesArray::from([
                FileTarget::class,
            ]),
        ],
    ],
    // LoggerInterface::class => static function () {
    //     $log = new \Monolog\Logger('test');
    //     $handler = new \Monolog\Handler\SocketHandler('127.0.0.1:9913');
    //     $handler->setFormatter(new \Monolog\Formatter\JsonFormatter());
    //     $log->pushHandler($handler);
    //
    //     return $log;
    // },
];
