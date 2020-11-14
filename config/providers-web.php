<?php

declare(strict_types=1);

use Yiisoft\Arrays\Modifier\ReverseBlockMerge;
use Yiisoft\Composer\Config\Builder;
use Yiisoft\Yii\Event\EventDispatcherProvider;

return [
    'yiisoft/event-dispatcher/eventdispatcher' => [
        '__class' => EventDispatcherProvider::class,
        'arguments' => [Builder::require('events-web')]
    ],

    ReverseBlockMerge::class => new ReverseBlockMerge(),
];
