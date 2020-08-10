<?php

use Yiisoft\Arrays\Modifier\ReverseBlockMerge;
use Yiisoft\Yii\Event\EventDispatcherProvider;

return [
    ReverseBlockMerge::class => new ReverseBlockMerge(),
    'yiisoft/event-dispatcher/eventdispatcher' => [
        '__class' => EventDispatcherProvider::class,
        '__construct()' => [$config['events-web']],
    ],
];
