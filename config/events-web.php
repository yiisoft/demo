<?php

declare(strict_types=1);

use App\Timer;
use Yiisoft\Arrays\Modifier\ReverseBlockMerge;
use Yiisoft\Yii\Web\Event\ApplicationStartup;

return [
    ApplicationStartup::class => [
        static fn (Timer $timer) => $timer->start('overall'),
    ],
    ReverseBlockMerge::class => new ReverseBlockMerge(),
];
