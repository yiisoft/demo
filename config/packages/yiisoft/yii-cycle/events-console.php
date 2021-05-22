<?php

declare(strict_types=1);

use Yiisoft\Yii\Cycle\Event\AfterMigrate;
use Yiisoft\Yii\Cycle\Listener\MigrationListener;

return [
    AfterMigrate::class => [
        [MigrationListener::class, 'onAfterMigrate'],
    ],
];
