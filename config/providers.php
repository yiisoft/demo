<?php

declare(strict_types=1);

use App\Provider\RepositoryProvider;
use Yiisoft\Composer\Config\Builder;
use Yiisoft\Yii\Event\EventDispatcherProvider;

return [
   'RepositoryProvider' => RepositoryProvider::class,

   'yiisoft/event-dispatcher/eventdispatcher' => [
        '__class' => EventDispatcherProvider::class,
        '__construct()' => [Builder::require('events-web')]
    ]
];
