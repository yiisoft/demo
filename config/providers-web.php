<?php

declare(strict_types=1);

/* @var array $params */

use Yiisoft\Arrays\Modifier\ReverseBlockMerge;
use Yiisoft\Composer\Config\Builder;
use Yiisoft\Yii\Event\EventDispatcherProvider;

return [
    ReverseBlockMerge::class => new ReverseBlockMerge(),
    'yiisoft/event-dispatcher/eventdispatcher' => [
        '__class' => EventDispatcherProvider::class,
        '__construct()' => [Builder::require('events-web')],
    ],
];
