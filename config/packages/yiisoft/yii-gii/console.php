<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\Log\LoggerInterface;
use Yiisoft\Aliases\Aliases;
use Yiisoft\View\Theme;
use Yiisoft\View\View;

return [
    EventDispatcherInterface::class => Yiisoft\EventDispatcher\Dispatcher\Dispatcher::class,
    ListenerProviderInterface::class => Yiisoft\EventDispatcher\Provider\Provider::class,
    View::class => static function (ContainerInterface $container) {
        return new View(
            $container->get(Aliases::class)->get('@views'),
            $container->get(Theme::class),
            $container->get(EventDispatcherInterface::class),
            $container->get(LoggerInterface::class)
        );
    },
];
