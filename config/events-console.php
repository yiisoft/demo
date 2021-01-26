<?php

declare(strict_types=1);

use App\Timer;
use Yiisoft\Arrays\Collection\ArrayCollection;
use Yiisoft\Arrays\Collection\Modifier\SaveOrder;
use Yiisoft\Yii\Console\Event\ApplicationStartup;

return new ArrayCollection(
    [
        ApplicationStartup::class => [
            static fn (Timer $timer) => $timer->start('overall'),
        ],
    ],
    new SaveOrder()
);
