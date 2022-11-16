<?php

declare(strict_types=1);

use Cycle\Database\Config\SQLite\FileConnectionConfig;
use Cycle\Database\Config\SQLiteDriverConfig;

return [
    'yiisoft/yii-cycle' => [
        // DBAL config
        'dbal' => [
            'connections' => [
                'sqlite' => new SQLiteDriverConfig(
                    new FileConnectionConfig(dirname(__DIR__, 2) . '/tests/Support/Data/database.db')
                ),
            ],
        ],
    ],
];
