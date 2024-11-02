<?php

declare(strict_types=1);

use Cycle\Database\Config\SQLite\FileConnectionConfig;
use Cycle\Database\Config\SQLiteDriverConfig;

return [
    'yiisoft/yii-debug' => [
        'enabled' => false,
    ],
    'yiisoft/yii-cycle' => [
        'dbal' => [
            'connections' => [
                'sqlite' => new SQLiteDriverConfig(
                    new FileConnectionConfig(dirname(__DIR__, 3) . '/tests/Support/Data/database.db')
                ),
            ],
        ],
    ],
];
