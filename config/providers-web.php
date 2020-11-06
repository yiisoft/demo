<?php

declare(strict_types=1);

use Yiisoft\Arrays\Collection\ArrayCollection;
use Yiisoft\Arrays\Collection\Modifier\SaveOrder;
use Yiisoft\Composer\Config\Builder;
use Yiisoft\Yii\Event\EventDispatcherProvider;

return new ArrayCollection(
    [
        'yiisoft/event-dispatcher/eventdispatcher' => [
            '__class' => EventDispatcherProvider::class,
            '__construct()' => [Builder::require('events-web')]
        ],
    ],
    new SaveOrder()
);
